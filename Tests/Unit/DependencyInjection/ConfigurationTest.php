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
                'amqp' => [
                    'class' => 'PhpAmqpLib\Connection\AMQPConnection',
                    'host' => '127.0.0.1',
                    'port' => 5672,
                    'user' => 'guest',
                    'password' => 'guest',
                ],
                'beanstalkd' => [
                    'class' => 'Pheanstalk\Pheanstalk',
                    'host' => '127.0.0.1',
                    'port' => 11300,
                ],
            ],
        ];

        // Case #1 Test default values.
        $out[] = [
            [
                'publishers' => [
                    'amqp' => [],
                    'beanstalkd' => [],
                ],
            ],
            $expectedConfiguration,
        ];

        // Case #2 Test merged configuration values.
        $out[] = [
            [
                'publishers' => [
                    'amqp' => [
                        'host' => 'localhost',
                        'port' => 5672,
                        'user' => 'guest',
                        'password' => 'guest',
                    ],
                    'beanstalkd' => [
                        'host' => '127.0.0.1',
                        'port' => 11300,
                    ],
                ],
            ],
            array_replace_recursive(
                $expectedConfiguration,
                [
                    'publishers' => [
                        'amqp' => ['host' => 'localhost'],
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
