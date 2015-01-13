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

        // Case #0 Check task publisher service.
        $out[] = [
            'ongr_task_messenger.task_publisher.default',
        ];
        // Case #1 Check AMQP service.
        $out[] = [
            'ongr_task_messenger.publisher.default.amqp',
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
     * Test publisher service not found exception.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class FooPublisherClass do not exist.
     */
    public function testPublisherServiceNotFoundException()
    {
        $config = [
            'ongr_task_messenger' => [
                'publishers' => [
                    'custom' => [
                        'custom' => [
                            'factory' => 'foo_factory',
                            'publisher' => 'foo_publisher_service',
                            'class' => 'FooPublisherClass',
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
     * Data provider for testing logging level.
     *
     * @return array
     */
    public function getTestLoggingLevelData()
    {
        $config = [
            'ongr_task_messenger' => [],
        ];

        $out = [];

        // Case #0 default value.
        $out[] = [
            'debug',
            array_merge_recursive(
                $config,
                [
                    'ongr_task_messenger' => [],
                ]
            ),
        ];

        // Case #1 specific value.
        $out[] = [
            'warning',
            array_merge_recursive(
                $config,
                [
                    'ongr_task_messenger' => [
                        'log_level' => 'warning',
                    ],
                ]
            ),
        ];

        return $out;
    }

    /**
     * Test logging level configuration.
     *
     * @param string $expectedLevel
     * @param array  $config
     *
     * @dataProvider getTestLoggingLevelData
     */
    public function testLogLevel($expectedLevel, $config)
    {
        $container = $this->getContainer();

        $extension = new ONGRTaskMessengerExtension();
        $extension->load($config, $container);

        $this->assertEquals(
            $expectedLevel,
            $container->getParameter('ongr_task_messenger.log_level'),
            "Default log_level value should be '{$expectedLevel}'"
        );
    }

    /**
     * Test non PSR log level value.
     */
    public function testNonPSRLogLevel()
    {
        $this->setExpectedExceptionRegExp('InvalidArgumentException', '/Invalid log_level value\..*/');
        $container = $this->getContainer();

        $config = [
            'ongr_task_messenger' => [
                'log_level' => 'foo',
            ],
        ];

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
                ],
            ],
        ];
    }
}
