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

use ONGR\ConnectionsBundle\Event\SyncTaskCompleteEvent;
use ONGR\TaskMessengerBundle\Document\SyncTask;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Predis;

class TaskPublisherTest extends WebTestCase
{
    /**
     * Dummy test for verifying that TaskPublisher works with configured brokers.
     */
    public function testPublish()
    {
        $client = self::createClient();
        $publisher = $client->getContainer()->get('ongr_task_messenger.task_publisher.foo_publisher');

        $task = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $task->setName('task_foo');
        $task->setCommand('command_foo');
        $publisher->publish($task);

        $redis = new Predis\Client();
        $this->verifyMessage($redis->get('test'), ['taskType' => 'task_foo', 'commandName' => 'command_foo -e test']);
    }

    /**
     * Test event listener which is registered with foo_publisher.
     */
    public function testListener()
    {
        $event = new SyncTaskCompleteEvent();
        $event->setTaskType(SyncTaskCompleteEvent::TASK_TYPE_CONVERT);
        $event->setInputFile('file.xml');
        $event->setProvider('test_provider');
        $event->setDataType(SyncTaskCompleteEvent::DATA_TYPE_FULL_DOCUMENTS);
        $event->setOutputFile('file.xml.converted.json');

        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $dispatcher->dispatch(SyncTaskCompleteEvent::EVENT_NAME, $event);

        $redis = new Predis\Client();
        $this->verifyMessage(
            $redis->get('test'),
            [
                'taskType' => SyncTaskCompleteEvent::TASK_TYPE_PUSH,
                'commandName' => 'ongr:sync:execute-file -e test file.xml.converted.json -p test_provider',
            ]
        );
    }

    /**
     * Test if correct redis message is returned.
     *
     * @param string $message
     * @param string $parameters
     */
    public function verifyMessage($message, $parameters)
    {
        $body = json_decode($message, true);

        $expectedTaskName = 'ongr.redis_task.' . $parameters['taskType'];
        $this->assertEquals($expectedTaskName, $body['task']);
        $this->assertEquals($parameters['commandName'], $body['args'][0]);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return self::createClient()->getContainer();
    }
}
