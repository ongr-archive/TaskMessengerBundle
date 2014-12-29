<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Service\Exception;

/**
 * Exception thrown on various AMQP failures.
 *
 * Depend on this exception type instead of explicitly on AMQP exceptions
 */
class PublisherConnectionException extends \RuntimeException
{
}
