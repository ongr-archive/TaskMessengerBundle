<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This is the class that validates and merges configuration from app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ongr_task_messenger');
        $rootNode
            ->children()
                ->scalarNode('test')
                ->end()
                ->append($this->getPublishersNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * Publishers configuration node.
     *
     * @return NodeDefinition
     * @throws InvalidConfigurationException
     */
    private function getPublishersNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('publishers');

        $node
            ->info('Defines publishers configuration settings.')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('amqp')
                        ->children()
                            ->booleanNode('enabled')
                                ->defaultTrue()
                            ->end()
                            ->scalarNode('class')
                                ->defaultValue('PhpAmqpLib\Connection\AMQPConnection')
                            ->end()
                            ->scalarNode('host')
                                ->defaultValue('127.0.0.1')
                            ->end()
                            ->scalarNode('port')
                                ->defaultValue('5672')
                            ->end()
                            ->scalarNode('user')
                                ->defaultValue('guest')
                            ->end()
                            ->scalarNode('password')
                                ->defaultValue('guest')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('beanstalkd')
                        ->children()
                            ->booleanNode('enabled')
                                ->defaultTrue()
                            ->end()
                            ->scalarNode('class')
                                ->defaultValue('Pheanstalk\Pheanstalk')
                            ->end()
                            ->scalarNode('host')
                                ->defaultValue('127.0.0.1')
                            ->end()
                            ->scalarNode('port')
                                ->defaultValue('11300')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
