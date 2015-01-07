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
 * Connection factory interface.
 */
interface ConnectionFactoryInterface
{
    /**
     * Connection factory create method.
     *
     * @return ConnectionFactoryInterface
     */
    public function create();
}
