<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new \ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \ONGR\ConnectionsBundle\ONGRConnectionsBundle(),
            new ONGR\TaskMessengerBundle\Tests\app\fixture\Acme\TestBundle\AcmeTestBundle(),
            new \ONGR\TaskMessengerBundle\ONGRTaskMessengerBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
        $loader->load(__DIR__ . '/config/parameters_' . $this->getEnvironment() . '.yml');
    }
}
