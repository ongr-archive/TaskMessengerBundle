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

/**
 * Interface TaskPublisherInterface.
 */
interface TaskPublisherInterface
{
    /**
     * Publishes task.
     *
     * @param SyncTask $task
     *
     * @return void
     */
    public function publish($task);
}
