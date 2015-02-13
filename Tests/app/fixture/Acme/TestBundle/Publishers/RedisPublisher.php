<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\app\fixture\Acme\TestBundle\Publishers;


use ONGR\TaskMessengerBundle\Document\SyncTask;
use ONGR\TaskMessengerBundle\Publishers\AbstractTaskPublisher;

class RedisPublisher extends AbstractTaskPublisher
{

    /**
     * Publishes task.
     *
     * @param SyncTask $task
     *
     * @return void
     */
    public function publish($task)
    {
        if (!$this->enabled) {
            return;
        }

        if (!$this->connection) {
            $this->connection = $this->connectionFactory->create();
        }
        $this->send($task);
    }

    /**
     * {@inheritdoc}
     */
    private function send($task)
    {
        $content = json_encode(
            [
                'id' => $task->getId(),
                'task' => 'ongr.redis_task.' . $task->getName(),
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

        $this->connection->set('test', $content);
    }
}
