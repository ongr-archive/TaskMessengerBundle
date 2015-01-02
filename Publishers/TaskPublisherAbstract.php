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

use ONGR\TaskMessengerBundle\Document\SyncTask;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Abstract class TaskPublisherAbstract.
 */
abstract class TaskPublisherAbstract implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var object.
     */
    protected $connection;

    /**
     * @var ConnectionFactory
     */
    protected $connectionFactory;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @param ConnectionFactory $connectionFactory
     * @param string            $environment
     */
    public function __construct($connectionFactory, $environment)
    {
        $this->environment = $environment;
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * Publishes task.
     *
     * @param SyncTask $task
     *
     * @return void
     */
    abstract public function publish($task);

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param SyncTask $task
     *
     * @return string
     */
    protected function getEnvironment($task)
    {
        $environment = $this->environment;

        if (strlen($task->getEnvironment()) > 0) {
            $environment = $task->getEnvironment();
        }

        return $environment;
    }
}
