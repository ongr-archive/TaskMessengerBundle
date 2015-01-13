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

        $this->setLogLevel($container, $config['log_level']);

        if (!empty($config['publishers'])) {
            foreach ($config['publishers'] as $taskPublisher => $publishers) {
                $taskPublisherDefinition = new Definition(
                    $container->getParameter('ongr_task_messenger.task_publisher.class')
                );
                $publisherId = sprintf('ongr_task_messenger.task_publisher.%s', $taskPublisher);

                foreach ($publishers as $name => $parameters) {
                    $this->validatePublisherServiceClass($parameters);
                    $this->setPublisherContainerParameters($container, $parameters, $taskPublisher, $name);

                    $factoryId = sprintf('ongr_task_messenger.publisher.factory.%s.%s', $taskPublisher, $name);
                    // Pass configuration parameters to factory service.
                    $factoryDefinition = $this->getFactoryDefinition($parameters['factory'], $parameters);
                    $container->setDefinition($factoryId, $factoryDefinition);

                    $concretePublisherId = sprintf('ongr_task_messenger.publisher.%s.%s', $taskPublisher, $name);
                    $container->setDefinition(
                        $concretePublisherId,
                        new Definition(
                            new Reference($parameters['publisher']),
                            [
                                new Definition(
                                    '%ongr_task_messenger.connection_factory.class%',
                                    [new Reference($factoryId)]
                                ),
                                '%kernel.environment%',
                            ]
                        )
                    );

                    $taskPublisherDefinition->addMethodCall('addPublisher', [new Reference($concretePublisherId)]);
                }
                $container->setDefinition($publisherId, $taskPublisherDefinition);
            }
        }
    }

    /**
     * Check if defined classes exists.
     *
     * @param array $parameters
     *
     * @throws InvalidArgumentException
     */
    private function validatePublisherServiceClass($parameters)
    {
        if (!class_exists($parameters['class'])) {
            throw new InvalidArgumentException(
                sprintf('Class %s do not exist.', $parameters['class'])
            );
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

    /**
     * Returns connection factory definition.
     *
     * @param string $factoryServiceId
     * @param array  $parameters
     *
     * @return Definition
     */
    public function getFactoryDefinition($factoryServiceId, $parameters)
    {
        $arguments = !empty($parameters['arguments']) ? $parameters['arguments'] : [];
        $factoryDefinition = new Definition(
            new Reference($factoryServiceId),
            [
                $parameters['class'],
                $parameters['host'],
                $parameters['port'],
                $parameters['user'],
                $parameters['password'],
                $arguments,
            ]
        );

        return $factoryDefinition;
    }

    /**
     * Set publishers logging level.
     *
     * @param ContainerBuilder $container
     * @param string           $logLevel
     *
     * @throws InvalidArgumentException
     */
    private function setLogLevel($container, $logLevel)
    {
        $logLevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        if (in_array($logLevel, $logLevels)) {
            $container->setParameter('ongr_task_messenger.log_level', $logLevel);
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid log_level value. Valid values: \'%s\'',
                    implode('\', \'', $logLevels)
                )
            );
        }
    }
}
