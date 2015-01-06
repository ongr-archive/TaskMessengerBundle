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
use Pheanstalk\Pheanstalk;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BeanstalkdPublisherTest extends WebTestCase
{
    /**
     * Test if BeanstalkdPublisher works as expected.
     */
    public function testLogging()
    {
        $container = $this->getContainer();

        $publisher = $container->get('ongr_task_messenger.publisher.default.beanstalkd');
        $logger = new NullLogger();
        $publisher->setLogger($logger);
        $task = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $task->setName('task_foo');
        $task->setCommand('command_foo');
        $publisher->publish($task);

        $pheanstalk = new Pheanstalk(
            $container->getParameter('ongr_task_messenger.publisher.default.beanstalkd.host'),
            $container->getParameter('ongr_task_messenger.publisher.default.beanstalkd.port')
        );
        $job = $pheanstalk
            ->watch('general')
            ->reserve();
        $jobData = json_decode($job->getData(), true);

        $this->assertEquals($jobData['task'], 'ongr.task.task_foo');
        $this->assertEquals($jobData['args'][0], 'command_foo -e test');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return self::createClient()->getContainer();
    }
}
