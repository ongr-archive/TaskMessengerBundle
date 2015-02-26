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

/**
 * Beanstalkd publisher implementation.
 */
class BeanstalkdPublisher extends AbstractTaskPublisher
{
    /**
     * Publish message to beanstalkd.
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
        } catch (\Exception $e) {
            throw new PublisherConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Send message to beanstalkd server.
     *
     * @param SyncTask $task
     */
    protected function send($task)
    {
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

        if ($this->connection) {
            $this->connection
                ->useTube($task->getExchange())
                ->put($content);
        }
        $this->logger && $this->logger->info(
            'beanstalkd publish',
            [$task->getName(), $task->getPublishingType(), $task->getHost()]
        );
    }
}
