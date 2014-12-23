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

use ONGR\TaskMessengerBundle\Service\CeleryPublisher;

class CeleryPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testMethodExists.
     *
     * @return array
     */
    public function getTestMethodExistsData()
    {
        $out = [];

        // Case #0: logger.
        $loggerMock = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $out[] = ['setLogger', $loggerMock];

        // Case #1: enabled.
        $out[] = ['setEnabled', true];

        return $out;
    }

    /**
     * Test to check whether method exists.
     *
     * @param string $method
     * @param string $parameter
     *
     * @dataProvider getTestMethodExistsData
     */
    public function testMethodExists($method, $parameter)
    {
        $publisher = $this->getPublisher();

        $this->assertTrue(method_exists($publisher, $method));
        $publisher->{$method}($parameter);
    }

    /**
     * Helper method to get CeleryPublisher object.
     *
     * @return CeleryPublisher
     */
    protected function getPublisher()
    {
        $connectionMock = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')
            ->disableOriginalConstructor()
            ->getMock();

        $environment = 'dummy-environment';

        $celeryPublisher = new CeleryPublisher($connectionMock, $environment);

        return $celeryPublisher;
    }
}
