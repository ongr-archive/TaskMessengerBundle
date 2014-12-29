<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Service;

use ONGR\TaskMessengerBundle\Document\SyncTask;
use ONGR\TaskMessengerBundle\Service\Exception\PublisherConnectionException;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Celery publisher implementation.
 */
class CeleryPublisher implements TaskPublisherInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var AMQPConnection
     */
    protected $connection;

    /**
     * @var ConnectionFactory
     */
    protected $connectionFactory;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @param AMQPConnection $connectionFactory
     * @param string         $environment
     */
    public function __construct($connectionFactory, $environment)
    {
        $this->environment = $environment;
        $this->connectionFactory = $connectionFactory;
    }

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
            $channel->exchange_declare($task->getExchange(), $task->getAmqpType(), false, true, false);

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
                [$task->getName(), $task->getAmqpType(), $task->getAmqpHost()]
            );

            $channel->basic_publish($message, $task->getExchange(), $task->getAmqpHost());
            $channel->close();
        }
    }

    /**
     * @param SyncTask $task
     *
     * @return string
     */
    protected function getEnvironment($task)
    {
        $environment = $this->environment;

        if (strlen($task->getEnvironment()) > 0) {
            $environment = $task->getEnvironment();
        }

        return $environment;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
    }
}
