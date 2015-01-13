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
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomAMQPPublisherTest extends WebTestCase
{
    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * Set up AMQP connection.
     */
    public function setUp()
    {
        $container = $this->getContainer();
        $exchangeName = 'general';

        $connection = new AMQPConnection(
            $container->getParameter('ongr_task_messenger.publisher.default.custom.host'),
            $container->getParameter('ongr_task_messenger.publisher.default.custom.port'),
            $container->getParameter('ongr_task_messenger.publisher.default.custom.user'),
            $container->getParameter('ongr_task_messenger.publisher.default.custom.password')
        );

        $this->channel = $connection->channel();
        list($queueName, ,) = $this->channel->queue_declare();
        $this->channel->queue_bind($queueName, $exchangeName, explode('.', gethostname())[0]);

        $this->channel->basic_consume(
            $queueName,
            getmypid(),
            false,
            true,
            true,
            true,
            [$this, 'verifyMessage']
        );
    }

    /**
     * Test if AMQPPublisher works as expected.
     */
    public function testPublish()
    {
        $container = $this->getContainer();

        $publisher = $container->get('ongr_task_messenger.publisher.default.custom');
        $logger = new NullLogger();
        $publisher->setLogger($logger);
        $task = new SyncTask(SyncTask::SYNC_TASK_PRESERVEHOST);
        $task->setName('task_foo');
        $task->setCommand('command_foo');
        $publisher->publish($task);

        $this->channel->wait();
    }

    /**
     * Test if correct AMQP message is returned.
     *
     * @param AMQPMessage $message
     */
    public function verifyMessage($message)
    {
        $body = json_decode($message->body, true);

        $this->assertEquals($body['task'], 'ongr.acme_task.task_foo');
        $this->assertEquals($body['args'][0], 'command_foo -e test');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return self::createClient()->getContainer();
    }
}
