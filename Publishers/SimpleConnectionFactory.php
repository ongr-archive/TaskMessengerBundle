<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Publishers;

/**
 * Class SimpleConnectionFactory.
 */
class SimpleConnectionFactory implements ConnectionFactoryInterface
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
     * @param string $class
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     */
    public function __construct($class, $host, $port, $user = null, $password = null)
    {
        $this->class = $class;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Creates publisher connection.
     *
     * @return object
     */
    public function create()
    {
        $class = $this->class;

        return new $class($this->host, $this->port, $this->user, $this->password);
    }
}
