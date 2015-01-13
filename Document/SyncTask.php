<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\TaskMessengerBundle\Document;

/**
 * Class defining Sync task.
 */
class SyncTask
{
    const SYNC_TASK_BROADCAST = 1;
    const SYNC_TASK_ROUNDROBIN = 2;
    const SYNC_TASK_PRESERVEHOST = 3;

    /**
     * @var string
     */
    protected $id = null;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var string
     */
    protected $publishingType;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $exchange;

    /**
     * @param int $type
     */
    public function __construct($type = self::SYNC_TASK_ROUNDROBIN)
    {
        $this->arguments = [];

        switch ($type) {
            case self::SYNC_TASK_BROADCAST:
                $this->setPublishingType('fanout');
                $this->setHost('');
                break;
            case self::SYNC_TASK_ROUNDROBIN:
                $this->setPublishingType('direct');
                $this->setHost('');
                break;
            case self::SYNC_TASK_PRESERVEHOST:
                $this->setPublishingType('direct');
                $this->setHost(explode('.', gethostname())[0]);
                break;
            default:
                // No other cases.
                break;
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        if ($this->id === null) {
            $this->id = md5(uniqid($this->getHost()));
        }

        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getName()
    {
        if ($this->name == '') {
            throw new \InvalidArgumentException('SyncTask name cannot be empty');
        }

        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getCommand()
    {
        $command = $this->command;

        if ($command == '') {
            switch ($this->name) {
                case 'download':
                    $command = 'ongr:sync:download';
                    break;
                case 'convert':
                    $command = 'ongr:sync:convert-file';
                    break;
                case 'push':
                    $command = 'ongr:sync:execute-file';
                    break;
                default:
                    throw new \InvalidArgumentException('SyncTask command cannot be empty');
            }
        }

        return $command;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getExchange()
    {
        if ($this->exchange === null) {
            switch ($this->name) {
                case 'download':
                    $exchange = 'download';
                    break;
                case 'convert':
                    $exchange = 'convert';
                    break;
                case 'push':
                    $exchange = 'push';
                    break;
                case 'live_search':
                    $exchange = 'live_search';
                    break;
                case 'scheduler':
                    $exchange = 'scheduler';
                    break;
                default:
                    $exchange = 'general';
                    break;
            }

            $this->exchange = $exchange;
        }

        return $this->exchange;
    }

    /**
     * @param string $exchange
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
    }

    /**
     * @return string
     */
    public function getPublishingType()
    {
        return $this->publishingType;
    }

    /**
     * @param string $publishingType
     */
    public function setPublishingType($publishingType)
    {
        $this->publishingType = $publishingType;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }
}
