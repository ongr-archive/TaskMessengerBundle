<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Publishers;

use ONGR\TaskMessengerBundle\Document\SyncTask;
use ONGR\TaskMessengerBundle\Publishers\Exception\PublisherConnectionException;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * AMQP publisher implementation.
 */
class AMQPPublisher extends TaskPublisherAbstract
{
    /**
     * Publish message to AMQP.
     *
     * @param SyncTask $task
     *
     * @throws PublisherConnectionException
     */
    public function publish($task)
    {
        try {
            if (!$this->enabled) {
                return;
            }

            if (!$this->connection) {
                $this->connection = $this->connectionFactory->create();
            }

            $this->send($task);
        } catch (AMQPExceptionInterface $e) {
            throw new PublisherConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Send message to AMQP server.
     *
     * @param SyncTask $task
     */
    protected function send($task)
    {
        $channel = $this->connection->channel();

        if ($channel) {
            $channel->exchange_declare($task->getExchange(), $task->getPublishingType(), false, true, false);

            $content = json_encode(
                [
                    'id' => $task->getId(),
                    'task' => 'ongr.task.' . $task->getName(),
                    'args' => [
                        implode(
                            ' ',
                            array_merge(
                                [
                                    $task->getCommand(),
                                    '-e',
                                    $this->getEnvironment($task),
                                ],
                                $task->getArguments()
                            )
                        ),
                    ],
                ]
            );

            $message = new AMQPMessage(
                $content,
                [
                    'content_type' => 'application/json',
                ]
            );

            $this->logger && $this->logger->info(
                'amqp publish',
                [$task->getName(), $task->getPublishingType(), $task->getHost()]
            );

            $channel->basic_publish($message, $task->getExchange(), $task->getHost());
            $channel->close();
        }
    }
}
