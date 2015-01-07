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

use ONGR\TaskMessengerBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for publishers configuration tests.
     *
     * @return array
     */
    public function getPublishersData()
    {
        $out = [];

        $expectedConfiguration = [
            'publishers' => [
                'default' => [
                    'amqp' => [
                        'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                        'publisher' => 'ongr_task_messenger.publisher.amqp',
                        'factory' => 'ongr_task_messenger.simple_connection_factory',
                        'host' => '127.0.0.1',
                        'port' => 5672,
                        'user' => 'guest',
                        'password' => 'guest',
                    ],
                    'beanstalkd' => [
                        'class' => 'Pheanstalk\Pheanstalk',
                        'publisher' => 'ongr_task_messenger.publisher.beanstalkd',
                        'factory' => 'ongr_task_messenger.simple_connection_factory',
                        'host' => '127.0.0.1',
                        'port' => 11300,
                        'user' => null,
                        'password' => null,
                    ],
                ],
                'fooPublisher' => [
                    'custom' => [
                        'publisher' => 'ongr_task_messenger.publisher.amqp',
                        'factory' => 'ongr_task_messenger.simple_connection_factory',
                        'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                        'port' => 123,
                        'host' => '127.0.0.1',
                        'user' => null,
                        'password' => null,
                        'arguments' => [],
                    ],
                ],
            ],
        ];

        // Case #0 Test default values.
        $out[] = [
            [
                'publishers' => [
                    'default' => [
                        'amqp' => [],
                    ],
                    'fooPublisher' => [
                        'custom' => [
                            'publisher' => 'ongr_task_messenger.publisher.amqp',
                            'factory' => 'ongr_task_messenger.simple_connection_factory',
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'port' => 123,
                        ],
                    ],
                ],
            ],
            [
                'publishers' => [
                    'default' => [
                        'amqp' => [
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'publisher' => 'ongr_task_messenger.publisher.amqp',
                            'factory' => 'ongr_task_messenger.simple_connection_factory',
                            'host' => '127.0.0.1',
                            'port' => 5672,
                            'user' => 'guest',
                            'password' => 'guest',
                        ],
                    ],
                    'fooPublisher' => [
                        'custom' => [
                            'publisher' => 'ongr_task_messenger.publisher.amqp',
                            'factory' => 'ongr_task_messenger.simple_connection_factory',
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'port' => 123,
                            'host' => '127.0.0.1',
                            'user' => null,
                            'password' => null,
                            'arguments' => [],
                        ],
                    ],
                ],
            ],
        ];

        // Case #1 Test merged configuration values.
        $out[] = [
            [
                'publishers' => [
                    'default' => [
                        'amqp' => [
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'host' => 'localhost',
                            'port' => 5672,
                            'user' => 'guest',
                            'password' => 'guest',
                        ],
                        'beanstalkd' => [
                            'class' => 'Foo\Bar\Beanstalkd',
                            'host' => '127.0.0.1',
                            'port' => 11300,
                            'user' => null,
                            'password' => null,
                        ],
                    ],
                    'fooPublisher' => [
                        'custom' => [
                            'publisher' => 'ongr_task_messenger.publisher.amqp',
                            'factory' => 'ongr_task_messenger.simple_connection_factory',
                            'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                            'port' => 123,
                        ],
                    ],
                ],
            ],
            array_replace_recursive(
                $expectedConfiguration,
                [
                    'publishers' => [
                        'default' => [
                            'amqp' => ['host' => 'localhost'],
                            'beanstalkd' => ['class' => 'Foo\Bar\Beanstalkd'],
                        ],
                        'fooPublisher' => [
                            'custom' => [
                                'publisher' => 'ongr_task_messenger.publisher.amqp',
                                'factory' => 'ongr_task_messenger.simple_connection_factory',
                                'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                                'port' => 123,
                                'user' => null,
                                'password' => null,
                            ],
                        ],
                    ],
                ]
            ),
        ];

        return $out;
    }

    /**
     * Test publishers configuration.
     *
     * @param array $configuration
     * @param array $expectedConfiguration
     *
     * @dataProvider getPublishersData
     */
    public function testPublishersConfiguration($configuration, $expectedConfiguration)
    {
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [$configuration]);

        $this->assertEquals(
            $processedConfig,
            $expectedConfiguration
        );
    }
}
