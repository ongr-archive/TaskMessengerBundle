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

class ONGRTaskMessengerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getTestDefinitionsData()
    {
        $out = [];

        // Case #0 simple configuration test.
        $out[] = [
            'ongr_task_messenger.sync_task_complete_listener',
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
        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition($definition));
    }

    /**
     * Test if publishers where added to task publisher service.
     */
    public function testTaggedServices()
    {
        $container = $this->getContainer();

        $extension = new ONGRTaskMessengerExtension();
        $extension->load([], $container);

        $taskPublisher = $container->findDefinition('ongr_task_messenger.task_publisher');
        $this->assertTrue($taskPublisher->hasMethodCall('addPublisher'));
    }

    /**
     * @return ContainerBuilder
     */
    protected function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }

    /**
     * Tests if right parameters are set.
     *
     * @param array $config
     * @param array $param
     * @param array $expected
     *
     * @dataProvider getTestParamsData
     */
    public function testPublishersParameters($config, $param, $expected)
    {
        $container = $this->getContainer();

        $extension = new ONGRTaskMessengerExtension();
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter($param), 'Expected parameter does not exist.');
        $this->assertEquals($expected, $container->getParameter($param), 'Parameter has been set with wrong value.');
    }

    /**
     * @return array
     */
    public function getTestParamsData()
    {
        $customConfig = $this->getPublishersConfigurationData();
        $customConfig = array_replace_recursive(
            $customConfig,
            [
                [
                    'publishers' => [
                        'amqp' => [
                            'class' => 'Foo\Bar\AMQPLib',
                        ],
                    ],
                ],
            ]
        );

        return [
            [
                [],
                'ongr_task_messenger.amqp_connection.class',
                'PhpAmqpLib\Connection\AMQPConnection',
            ],
            [
                $customConfig,
                'ongr_task_messenger.amqp_connection.class',
                'Foo\Bar\AMQPLib',
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
            ],
        ];
    }
}
