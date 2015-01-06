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

use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\PrototypedArrayNode;

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
            ->info('Defines task publishers manager node.')
                ->prototype('array')
                ->info('Defines publishers and configuration settings.')
                    ->children()
                        ->arrayNode('amqp')
                            ->children()
                                ->scalarNode('class')
                                    ->defaultValue('PhpAmqpLib\Connection\AMQPConnection')
                                ->end()
                                ->scalarNode('host')
                                    ->defaultValue('127.0.0.1')
                                ->end()
                                ->scalarNode('port')
                                    ->defaultValue(5672)
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
                                ->scalarNode('class')
                                    ->defaultValue('Pheanstalk\Pheanstalk')
                                ->end()
                                ->scalarNode('host')
                                    ->defaultValue('127.0.0.1')
                                ->end()
                                ->scalarNode('port')
                                    ->defaultValue(11300)
                                ->end()
                                ->scalarNode('user')
                                    ->defaultValue(null)
                                ->end()
                                ->scalarNode('password')
                                    ->defaultValue(null)
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('custom')
                            ->children()
                                ->scalarNode('class')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('publisher')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('host')
                                    ->defaultValue('127.0.0.1')
                                ->end()
                                ->scalarNode('port')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('user')
                                    ->defaultValue(null)
                                ->end()
                                ->scalarNode('password')
                                    ->defaultValue(null)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
            ->end();

        return $node;
    }
}
