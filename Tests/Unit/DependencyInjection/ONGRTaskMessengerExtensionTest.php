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
        $container = new ContainerBuilder();

        $extension = new ONGRTaskMessengerExtension();
        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition($definition));
    }
}
