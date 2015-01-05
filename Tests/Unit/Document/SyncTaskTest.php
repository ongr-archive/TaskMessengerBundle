<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\Unit;

use ONGR\TaskMessengerBundle\Document\SyncTask;

class SyncTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getTestSetGetData()
    {
        return [
            ['id', '1'],
            ['name', 'push'],
            ['arguments', [1, 2]],
            ['environment', 'environment'],
            ['publishingType', 'publishingType'],
            ['host', 'host'],
            ['command', 'command'],
            ['exchange', 'foo'],
        ];
    }

    /**
     * Tests setters and getters.
     *
     * @param string $property
     * @param mixed  $argument
     *
     * @dataProvider getTestSetGetData
     */
    public function testSetGet($property, $argument)
    {
        $event = new SyncTask(SyncTask::SYNC_TASK_ROUNDROBIN);

        $setter = 'set' . ucfirst($property);
        $getter = 'get' . ucfirst($property);

        $this->assertTrue(method_exists($event, $setter), 'Setter not exists');
        $this->assertTrue(method_exists($event, $getter), 'Getter not exists');
        $event->$setter($argument);
        $this->assertEquals($argument, $event->$getter());
    }

    /**
     * Test for getExchange and getCommand methods.
     *
     * @param string $name
     * @param string $expectedExchange
     * @param string $expectedCommand
     * @param string $exchange
     *
     * @dataProvider getTestExchangeCommandData
     */
    public function testExchangeCommand($name, $expectedExchange, $expectedCommand = null, $exchange = null)
    {
        $task = new SyncTask(SyncTask::SYNC_TASK_BROADCAST);
        $task->setName($name);
        $task->setExchange($exchange);

        $this->assertEquals($expectedExchange, $task->getExchange());

        if ($expectedCommand) {
            $this->assertEquals($expectedCommand, $task->getCommand());
        }
    }

    /**
     * Data provider for testExchangeCommand.
     *
     * @return array
     */
    public function getTestExchangeCommandData()
    {
        $out = [];

        // Case #0: download.
        $out[] = ['download', 'download', 'ongr:sync:download'];

        // Case #1: convert.
        $out[] = ['convert', 'convert', 'ongr:sync:convert-file'];

        // Case #2: push.
        $out[] = ['push', 'push', 'ongr:sync:execute-file'];

        // Case #3: scheduler.
        $out[] = ['scheduler', 'scheduler'];

        // Case #4: general.
        $out[] = ['general', 'general'];

        // Case #5: other.
        $out[] = ['other', 'foo', '', 'foo'];

        return $out;
    }

    /**
     * Method to test getCommand and getExchange methods with live-search case.
     */
    public function testExchangeCommandLiveSearch()
    {
        $name = 'live_search';
        $expectedExchange = 'live_search';

        $task = new SyncTask(SyncTask::SYNC_TASK_BROADCAST);
        $task->setName($name);

        $this->assertEquals($expectedExchange, $task->getExchange());

        $this->setExpectedException('InvalidArgumentException');
        $task->getCommand();
    }

    /**
     * Method to test whether exception is being thrown when task name is not set.
     */
    public function testGetNameException()
    {
        $task = new SyncTask(SyncTask::SYNC_TASK_BROADCAST);
        $this->setExpectedException('InvalidArgumentException');
        $task->getName();
    }
}
