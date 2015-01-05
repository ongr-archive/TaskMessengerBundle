<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\Unit\Publishers;

use ONGR\TaskMessengerBundle\Document\SyncTask;
use ONGR\TaskMessengerBundle\Publishers\BeanstalkdPublisher;

class BeanstalkdPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests connection factory exception handling.
     *
     * @expectedException \ONGR\TaskMessengerBundle\Publishers\Exception\PublisherConnectionException
     */
    public function testConnectionFactoryException()
    {
        $exception = $this->getMockBuilder('\Exception')
            ->disableOriginalConstructor()
            ->getMock();

        $connectionFactory = $this->getMockBuilder('ONGR\TaskMessengerBundle\Publishers\ConnectionFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionFactory
            ->expects($this->once())
            ->method('create')
            ->willThrowException($exception);

        $environment = 'dummy-environment';

        $publisher = new BeanstalkdPublisher($connectionFactory, $environment);

        $task = new SyncTask(SyncTask::SYNC_TASK_BROADCAST);
        $task->setName('ongr:sync:download');
        $expectedEnvironment = 'test';
        $task->setEnvironment($expectedEnvironment);

        $publisher->publish($task);
    }

    /**
     * Test to check if disabled publisher do not publish.
     */
    public function testDisabledPublisher()
    {
        $publisher = $this->getMockBuilder('ONGR\TaskMessengerBundle\Publishers\BeanstalkdPublisher')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $publisher->setEnabled(false);
        $task = new SyncTask(SyncTask::SYNC_TASK_BROADCAST);
        $task->setName('ongr:sync:download');
        $publisher->expects($this->never())->method('send');

        $publisher->publish($task);
    }
}
