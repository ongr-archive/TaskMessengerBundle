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
use ONGR\TaskMessengerBundle\Publishers\AbstractTaskPublisher;

/**
 * Publishes messages to all enabled publishers.
 */
class TaskPublisher
{
    /**
     * @var AbstractTaskPublisher
     */
    protected $publishers;

    /**
     * @param AbstractTaskPublisher $publisher
     */
    public function __construct(AbstractTaskPublisher $publisher = null)
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
     * @return AbstractTaskPublisher[]
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @param AbstractTaskPublisher $publisher
     */
    public function addPublisher($publisher)
    {
        $this->publishers[] = $publisher;
    }
}
