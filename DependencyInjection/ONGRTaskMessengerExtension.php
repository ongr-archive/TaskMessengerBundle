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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages bundle configuration.
 */
class ONGRTaskMessengerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (isset($config['publishers'])) {
            foreach ($config['publishers'] as $publisher => $parameters) {
                foreach ($parameters as $key => $value) {
                    $parameter = sprintf('ongr_task_messenger.%s_connection.%s', $publisher, $key);
                    if ($container->hasParameter($parameter)) {
                        $container->setParameter($parameter, $value);
                    }
                    $container->setParameter(
                        sprintf('ongr_task_messenger.task_publisher.%s.enabled', $publisher),
                        true
                    );
                }
            }
        }

        $taskPublisher = $container->findDefinition('ongr_task_messenger.task_publisher');
        $taggedPublishers = $container->findTaggedServiceIds('ongr_task_messenger.task_publisher');
        foreach ($taggedPublishers as $id => $tags) {
            $taskPublisher->addMethodCall(
                'addPublisher',
                [new Reference($id)]
            );
        }
    }
}
