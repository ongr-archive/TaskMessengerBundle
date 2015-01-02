<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\Functional\Publishers;

use ONGR\TaskMessengerBundle\Document\SyncTask;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AMQPPublisherTest extends WebTestCase
{
    /**
     * Test if AMQPPublisher works as expected.
     */
    public function testLogging()
    {
        $client = self::createClient();

        $publisher = $client->getContainer()->get('ongr_task_messenger.task_publisher.amqp');
        $logger = new NullLogger();
        $publisher->setLogger($logger);
        $task = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $task->setName('task_foo');
        $task->setCommand('command_foo');
        $publisher->publish($task);
    }
}
