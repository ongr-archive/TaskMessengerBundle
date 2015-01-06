<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\Unit\DependencyInjection;

use ONGR\TaskMessengerBundle\DependencyInjection\ONGRTaskMessengerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class ONGRTaskMessengerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getTestDefinitionsData()
    {
        $out = [];

        // Case #0 Check task listener service.
        $out[] = [
            'ongr_task_messenger.sync_task_complete_listener',
        ];
        // Case #1 Check task publisher service.
        $out[] = [
            'ongr_task_messenger.task_publisher.default',
        ];
        // Case #2 Check second task publisher service.
        $out[] = [
            'ongr_task_messenger.task_publisher.custom',
        ];
        // Case #3 Check AMQP service.
        $out[] = [
            'ongr_task_messenger.publisher.default.amqp',
        ];
        // Case #4 Check custom AMQP service.
        $out[] = [
            'ongr_task_messenger.publisher.custom.custom',
        ];

        return $out;
    }

    /**
     * Tests if definition is set.
     *
     * @param string $definition
     *
     * @dataProvider getTestDefinitionsData
     */
    public function testDefinitions($definition)
    {
        $container = $this->getContainer();

        $extension = new ONGRTaskMessengerExtension();
        $extension->load($this->getPublishersConfigurationData(), $container);

        $this->assertTrue($container->hasDefinition($definition));
    }

    /**
     * Test if publisher where added to default task publisher service.
     */
    public function testTaskPublisherPublishers()
    {
        $container = $this->getContainer();

        $config = [
            'ongr_task_messenger' => [
                'publishers' => [
                    'default' => [
                        'amqp' => [
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'host' => '127.0.0.1',
                            'port' => 5672,
                            'user' => 'guest',
                            'password' => 'guest',
                        ],
                    ],
                ],
            ],
        ];

        $extension = new ONGRTaskMessengerExtension();
        $extension->load($config, $container);

        $taskPublisher = $container->findDefinition('ongr_task_messenger.task_publisher.default');
        $this->assertTrue($taskPublisher->hasMethodCall('addPublisher'));
    }

    /**
     * @return ContainerBuilder
     */
    protected function getContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        return $container;
    }

    /**
     * Tests if right parameters are set.
     *
     * @param array $param
     * @param array $expected
     *
     * @dataProvider getTestParamsData
     */
    public function testPublishersParameters($param, $expected)
    {
        $container = $this->getContainer();

        $extension = new ONGRTaskMessengerExtension();
        $extension->load([], $container);

        $this->assertTrue($container->hasParameter($param), 'Expected parameter does not exist.');
        $this->assertEquals($expected, $container->getParameter($param), 'Parameter has been set with wrong value.');
    }

    /**
     * Test for publisher class not found exception.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Publisher FooAMQPPublisher class do not exist.
     */
    public function testPublisherClassNotFoundException()
    {
        $config = [
            'ongr_task_messenger' => [
                'publishers' => [
                    'custom' => [
                        'custom' => [
                            'publisher' => 'FooAMQPPublisher',
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'host' => '127.0.0.1',
                            'port' => 5672,
                        ],
                    ],
                ],
            ],
        ];

        $container = $this->getContainer();

        $extension = new ONGRTaskMessengerExtension();
        $extension->load($config, $container);
    }

    /**
     * @return array
     */
    public function getTestParamsData()
    {
        return [
            [
                'ongr_task_messenger.connection_factory.class',
                'ONGR\TaskMessengerBundle\Publishers\ConnectionFactory',
            ],
            [
                'ongr_task_messenger.task_publisher.class',
                'ONGR\TaskMessengerBundle\Service\TaskPublisher',
            ],
            [
                'ongr_task_messenger.publisher.amqp.class',
                'ONGR\TaskMessengerBundle\Publishers\AMQPPublisher',
            ],
            [
                'ongr_task_messenger.publisher.beanstalkd.class',
                'ONGR\TaskMessengerBundle\Publishers\BeanstalkdPublisher',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getPublishersConfigurationData()
    {
        return [
            [
                'publishers' => [
                    'default' => [
                        'amqp' => [
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'host' => '127.0.0.1',
                            'port' => 5672,
                            'user' => 'guest',
                            'password' => 'guest',
                        ],
                    ],
                    'custom' => [
                        'custom' => [
                            'publisher' => 'ONGR\TaskMessengerBundle\Tests\app\fixture\Acme\TestBundle\Publishers\CustomAMQPPublisher',
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'host' => '127.0.0.1',
                            'port' => 5672,
                        ],
                    ],
                ],
            ],
        ];
    }
}
