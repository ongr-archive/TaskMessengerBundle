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
use ONGR\TaskMessengerBundle\Publishers\TaskPublisherAbstract;

/**
 * Publishes messages to all enabled publishers.
 */
class TaskPublisher
{
    /**
     * @var TaskPublisherAbstract
     */
    protected $publishers;

    /**
     * @param TaskPublisherAbstract $publisher
     */
    public function __construct(TaskPublisherAbstract $publisher = null)
    {
        if ($publisher !== null) {
            $this->publishers = [$publisher];
        }
    }

    /**
     * Publish task trough publishers.
     *
     * @param SyncTask $task
     */
    public function publish($task)
    {
        foreach ($this->getPublishers() as $publisher) {
            $publisher->publish($task);
        }
    }

    /**
     * @return TaskPublisherAbstract[]
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @param TaskPublisherAbstract $publisher
     */
    public function addPublisher($publisher)
    {
        $this->publishers[] = $publisher;
    }
}
