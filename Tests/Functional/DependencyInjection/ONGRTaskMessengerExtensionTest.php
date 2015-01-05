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
        $this->assertTrue($container->has('ongr_task_messenger.task_publisher'));

        $this->assertTrue($container->has('ongr_task_messenger.task_publisher.amqp'));
        $this->assertTrue($container->has('ongr_task_messenger.task_publisher.beanstalkd'));

        $taskPublisher = $container->get('ongr_task_messenger.task_publisher');

        $this->assertEquals(2, count($taskPublisher->getPublishers()), 'There should be 2 publishers configured.');
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
