<?php

namespace Dodo\Redis\Connection;

use Dodo\Redis\Config\Configuration;
use Dodo\Redis\Exception\RedisClientException;

class RedisConnection implements ConnectionInterface
{
    /**
     * @var Configuration $parameters
     */
    private $config;

    /**
     * @var \Redis $connection
     */
    private $connection;

    /**
     * @var bool $state
     */
    private $state = false;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->connection = new \Redis();
    }

    /**
     * Set connection state
     * 
     * @return void
     */
    private function setConnectionState(bool $value): void
    {
        $this->state = $value;
    }

    /**
     * Open Redis connection
     * 
     * @return void
     */
    public function open(): void
    {
        if ($this->isOpen()) return;

        $debug = $this->config->get("debug");
        $host = $this->config->get("host");
        $port = $this->config->get("port");

        try {
            $this->connection->connect($host, $port);
            $this->setConnectionState(true);
        } catch(\RedisException $e) {
            if ($debug) throw new RedisClientException($e->getMessage());            
        }
    }

    /**
     * Return Redis connection instance
     * 
     * @return \Redis
     */
    public function redis()
    {
        return $this->connection;
    }

    /**
     * Close Redis connection
     * 
     * @return void
     */
    public function close(): void
    {
        if (!$this->isOpen()) return;

        $this->connection->close();
        $this->setConnectionState(false);
    }

    /**
     * Check if Redis connection is open
     * 
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->state;
    }
}