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

use ONGR\ConnectionsBundle\Event\SyncTaskCompleteEvent;
use ONGR\TaskMessengerBundle\Document\SyncTask;
use ONGR\TaskMessengerBundle\Service\SyncTasksListener;

class SyncTaskListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getTestHandleEventData()
    {
        $out = [];

        // Case #0 convert.
        $event = new SyncTaskCompleteEvent();
        $event->setTaskType(SyncTaskCompleteEvent::TASK_TYPE_CONVERT);
        $event->setInputFile('file.xml');
        $event->setProvider('testProvider');
        $event->setDataType(SyncTaskCompleteEvent::DATA_TYPE_FULL_DOCUMENTS);
        $event->setOutputFile('file.xml.converted.json');
        $expected = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $expected->setName(SyncTaskCompleteEvent::TASK_TYPE_PUSH);
        $expected->setArguments(
            ['file.xml.converted.json', '-p', 'testProvider']
        );
        $out[] = [$event, $expected];

        // Case #1 download.
        $event = new SyncTaskCompleteEvent();
        $event->setTaskType(SyncTaskCompleteEvent::TASK_TYPE_DOWNLOAD);
        $event->setProvider('testProvider');
        $event->setDataType(SyncTaskCompleteEvent::DATA_TYPE_FULL_DOCUMENTS);
        $event->setOutputFile('file.xml');
        $event->setDataDescription('delta');
        $expected = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $expected->setName(SyncTaskCompleteEvent::TASK_TYPE_CONVERT);
        $expected->setArguments(['testProvider', 'file.xml', '-t', 'delta']);
        $out[] = [$event, $expected];

        // Case #2 push.
        $event = new SyncTaskCompleteEvent();
        $event->setTaskType(SyncTaskCompleteEvent::TASK_TYPE_PUSH);
        $event->setProvider('testProvider');
        $event->setDataType(SyncTaskCompleteEvent::DATA_TYPE_FULL_DOCUMENTS);
        $out[] = [$event];

        // Case #3 convert partial.
        $event = new SyncTaskCompleteEvent();
        $event->setTaskType(SyncTaskCompleteEvent::TASK_TYPE_CONVERT);
        $event->setInputFile('file.xml');
        $event->setProvider('testProvider');
        $event->setDataType(SyncTaskCompleteEvent::DATA_TYPE_PARTIAL_DOCUMENTS);
        $event->setOutputFile('file.xml.converted.json');
        $expected = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $expected->setName(SyncTaskCompleteEvent::TASK_TYPE_PUSH);
        $expected->setArguments(
            ['file.xml.converted.json', '-p', 'testProvider', '-d']
        );
        $out[] = [$event, $expected];

        return $out;
    }

    /**
     * Tests handle event with multiple cases.
     *
     * @param SyncTaskCompleteEvent $event    Event to handle.
     * @param null|SyncTask         $expected Sync task to publish.
     *
     * @dataProvider getTestHandleEventData
     */
    public function testHandleEvent($event, $expected = null)
    {
        $publisher = $this->getMock('ONGR\TaskMessengerBundle\Service\TaskPublisherInterface', ['publish']);

        if ($expected) {
            $publisher->expects($this->once())->method('publish')->with($expected);
        } else {
            $publisher->expects($this->never())->method('publish');
        }

        $listener = new SyncTasksListener($publisher);
        $listener->handleEvent($event);
    }
}
