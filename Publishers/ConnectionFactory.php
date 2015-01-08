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
 * Instantiates connection object.
 *
 * Factory is needed because if broker is down or not available, exception is thrown in a connection object
 * constructor. If connection is a service itself, whole container fails upon creation time.
 *
 * Connection service also cannot be a lazy service, because PHPUnit is calling __destruct on all services afterwards,
 * which instantiates the failing connection.
 */
class ConnectionFactory
{
    /**
     * @var ConnectionFactoryInterface
     */
    public $factory;

    /**
     * @param ConnectionFactoryInterface $factory
     */
    public function __construct(ConnectionFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns connection object.
     *
     * @return mixed
     */
    public function create()
    {
        return $this->factory->create();
    }
}
