<?php

namespace Dodo\Redis\Connection;

use Dodo\Redis\Config\Configuration;

class Factory
{
    /**
     * Redis connection schemes
     * 
     * @var array $schemes
     */
    private static $schemes = [
        "redis" => "\\Dodo\\Redis\\Connection\\RedisConnection"
    ];

    /**
     * Return a new connection instance
     * with chosen scheme
     * 
     * @param string $scheme
     * @param string $host
     * @param int $port
     * @return ConnectionInterface
     */
    public static function create(string $scheme, array $configuration)
    {
        $class = static::$schemes[$scheme] ?? "redis";

        return new $class(new Configuration($configuration));
    }
}