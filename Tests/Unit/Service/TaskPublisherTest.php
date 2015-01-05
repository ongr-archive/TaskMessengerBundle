<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\Unit\Service;

use ONGR\TaskMessengerBundle\Document\SyncTask;
use ONGR\TaskMessengerBundle\Service\TaskPublisher;

class TaskPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if publisher is added correctly.
     */
    public function testTaskPublisherConstructor()
    {
        $task = new SyncTask(SyncTask::SYNC_TASK_BROADCAST);
        $task->setName('ongr:sync:download');
        $task->setCommand('ongr:sync:download');

        $amqpPublisher = $this->getAMQPPublisher();
        $amqpPublisher->expects($this->once())->method('publish')->with($task);

        /** @var TaskPublisher $taskPublisher */
        $taskPublisher = $this->getMockBuilder('ONGR\TaskMessengerBundle\Service\TaskPublisher')
            ->setConstructorArgs([$amqpPublisher])
            ->setMethods(null)
            ->getMock();

        $taskPublisher->publish($task);
    }

    /**
     * Returns AMQPPublisher mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getAMQPPublisher()
    {
        return $this->getMockBuilder('ONGR\TaskMessengerBundle\Publishers\AMQPPublisher')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
