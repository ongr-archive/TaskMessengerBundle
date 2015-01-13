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

use ONGR\ConnectionsBundle\Event\SyncTaskCompleteEvent;
use ONGR\TaskMessengerBundle\Document\SyncTask;

/**
 * Sync Task listener class.
 */
class SyncTasksListener
{
    /**
     * @var TaskPublisher
     */
    protected $publisher;

    /**
     * @param TaskPublisher $publisher
     */
    public function __construct($publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * Handles sync task complete event.
     *
     * @param SyncTaskCompleteEvent $event
     */
    public function handleEvent($event)
    {
        switch ($event->getTaskType()) {
            case SyncTaskCompleteEvent::TASK_TYPE_DOWNLOAD:
                $task = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
                $task->setName(SyncTaskCompleteEvent::TASK_TYPE_CONVERT);

                $task->setArguments(
                    [$event->getProvider(), $event->getOutputFile()]
                );

                if ($event->getDataDescription()) {
                    $task->setArguments(array_merge($task->getArguments(), ['-t', $event->getDataDescription()]));
                }

                $this->publisher->publish($task);
                break;
            case SyncTaskCompleteEvent::TASK_TYPE_CONVERT:
                $task = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
                $task->setName(SyncTaskCompleteEvent::TASK_TYPE_PUSH);

                $task->setArguments(
                    [$event->getOutputFile(), '-p', $event->getProvider()]
                );

                switch ($event->getDataType()) {
                    case SyncTaskCompleteEvent::DATA_TYPE_PARTIAL_DOCUMENTS:
                        $task->setArguments(array_merge($task->getArguments(), ['-d']));
                        break;
                    default:
                        // No other cases.
                        break;
                }

                $this->publisher->publish($task);
                break;
            default:
                // No other cases.
                break;
        }
    }
}
