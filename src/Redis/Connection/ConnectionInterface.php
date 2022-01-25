<?php

namespace Dodo\Redis\Connection;

use Dodo\Redis\Config\Configuration;

interface ConnectionInterface
{
    public function __construct(Configuration $configuration);

    /**
     * Open Redis connection
     * 
     * @return void
     */
    public function open(): void;

    /**
     * Return Redis connection instance
     * 
     * @return mixed
     */
    public function redis();

    /**
     * Close Redis connection
     * 
     * @return void
     */
    public function close(): void;

    /**
     * Check if Redis connection is open
     * 
     * @return bool
     */
    public function isOpen(): bool;
}