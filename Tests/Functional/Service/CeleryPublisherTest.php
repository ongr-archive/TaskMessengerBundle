<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\Functional\Service;

use ONGR\TaskMessengerBundle\Document\SyncTask;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Log\NullLogger;

class CeleryPublisherTest extends WebTestCase
{
    /**
     * Dummy test for verifying that integration with AMQP broker does not fail.
     */
    public function testPublish()
    {
        $client = self::createClient();
        $publisher = $client->getContainer()->get('ongr_task_messenger.task_publisher');
        $publisher->setLogger(new NullLogger());

        $task = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $task->setName('task_foo');
        $task->setCommand('command_foo');
        $publisher->publish($task);
    }
}
