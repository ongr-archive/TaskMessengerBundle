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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
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

        if (!empty($config['publishers'])) {
            $factoryClass = $container->getParameter('ongr_task_messenger.connection_factory.class');
            foreach ($config['publishers'] as $taskPublisher => $publishers) {
                $taskPublisherDefinition = new Definition(
                    $container->getParameter('ongr_task_messenger.task_publisher.class')
                );

                $publisherId = sprintf('ongr_task_messenger.task_publisher.%s', $taskPublisher);
                foreach ($publishers as $name => $parameters) {
                    $this->validatePublisherConfiguration($parameters);
                    $this->setPublisherContainerParameters($container, $parameters, $taskPublisher, $name);

                    $factoryId = sprintf('ongr_task_messenger.publisher.factory.%s.%s', $taskPublisher, $name);
                    $factoryDefinition = $this->getFactoryDefinition($factoryClass, $parameters);

                    $container->setDefinition($factoryId, $factoryDefinition);

                    $publisherClassId = sprintf('ongr_task_messenger.publisher.%s.class', $name);
                    if (!$container->hasParameter($publisherClassId)) {
                        $publisherClass = $parameters['publisher'];
                    } else {
                        $publisherClass = $container->getParameter($publisherClassId);
                    }

                    $publisherDefinition = new Definition(
                        $publisherClass,
                        [
                            new Reference($factoryId),
                            $container->getParameter('kernel.environment'),
                        ]
                    );
                    $concretePublisherId = sprintf('ongr_task_messenger.publisher.%s.%s', $taskPublisher, $name);
                    $container->setDefinition($concretePublisherId, $publisherDefinition);

                    $taskPublisherDefinition->addMethodCall('addPublisher', [new Reference($concretePublisherId)]);
                }
                $container->setDefinition($publisherId, $taskPublisherDefinition);
            }
        }
    }

    /**
     * Returns connection factory definition.
     *
     * @param string $factoryClass
     * @param array  $parameters
     *
     * @return Definition
     */
    public function getFactoryDefinition($factoryClass, $parameters)
    {
        $factoryDefinition = new Definition(
            $factoryClass,
            [
                $parameters['class'],
                $parameters['host'],
                $parameters['port'],
                $parameters['user'],
                $parameters['password'],
            ]
        );

        return $factoryDefinition;
    }

    /**
     * Check if defined classes exists.
     *
     * @param array $parameters
     *
     * @throws InvalidArgumentException
     */
    private function validatePublisherConfiguration($parameters)
    {
        $keys = ['class', 'publisher'];

        foreach ($keys as $key) {
            if (!empty($parameters[$key])) {
                if (!class_exists($parameters[$key])) {
                    throw new InvalidArgumentException(
                        sprintf('Publisher %s class do not exist.', $parameters['publisher'])
                    );
                }
            }
        }
    }

    /**
     * Set publisher configuration values to container.
     *
     * @param ContainerBuilder $container
     * @param array            $parameters
     * @param string           $taskPublisher
     * @param string           $name
     */
    private function setPublisherContainerParameters(ContainerBuilder $container, $parameters, $taskPublisher, $name)
    {
        foreach ($parameters as $key => $value) {
            $container->setParameter(
                sprintf('ongr_task_messenger.publisher.%s.%s.%s', $taskPublisher, $name, $key),
                $value
            );
        }
    }
}
