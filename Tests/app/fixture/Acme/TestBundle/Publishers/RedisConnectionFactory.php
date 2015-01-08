<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Tests\app\fixture\Acme\TestBundle\Publishers;

use ONGR\TaskMessengerBundle\Publishers\ConnectionFactoryInterface;

/**
 * Custom RedisConnectionFactory.
 */
class RedisConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @param string $class
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param array  $arguments
     */
    public function __construct(
        $class = null,
        $host = null,
        $port = null,
        $user = null,
        $password = null,
        $arguments = []
    ) {
        $this->class = $class;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->arguments = $arguments;
    }

    /**
     * Connection factory create method.
     *
     * @return ConnectionFactoryInterface
     */
    public function create()
    {
        $class = $this->class;

        return new $class(
            [
                'scheme' => $this->arguments['scheme'],
                'host' => $this->host,
                'port' => $this->port,
            ]
        );
    }
}
