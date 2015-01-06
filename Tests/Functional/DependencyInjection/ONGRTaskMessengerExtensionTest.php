<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\Functional\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ONGRTaskMessengerExtensionTest extends WebTestCase
{
    /**
     * Test if TaskPublisher service was created.
     */
    public function testTaggedServices()
    {
        $container = $this->getContainer();

        $this->assertTrue($container->has('ongr_task_messenger.task_publisher.default'));
        $this->assertTrue($container->has('ongr_task_messenger.publisher.default.amqp'));
        $this->assertTrue($container->has('ongr_task_messenger.publisher.default.beanstalkd'));

        $this->assertTrue($container->has('ongr_task_messenger.task_publisher.foo_publisher'));
        $this->assertTrue($container->has('ongr_task_messenger.publisher.foo_publisher.custom'));

        $defaultPublisher = $container->get('ongr_task_messenger.task_publisher.default');
        $this->assertEquals(2, count($defaultPublisher->getPublishers()), 'There should be 2 publishers configured.');

        $fooPublisher = $container->get('ongr_task_messenger.task_publisher.foo_publisher');
        $this->assertEquals(1, count($fooPublisher->getPublishers()), 'There should be 1 publisher configured.');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        $container = self::createClient()->getContainer();

        return $container;
    }
}
