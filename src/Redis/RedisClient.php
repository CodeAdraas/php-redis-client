<?php

namespace Dodo\Redis;

use Dodo\Redis\Exception\RedisClientException;
use Dodo\Redis\Command\Commands;
use Dodo\Redis\Config\Configuration;
use Dodo\Redis\Connection\Factory;
use Dodo\Redis\Connection\ConnectionInterface;
use Dodo\Redis\Command\Core\Executor;

class RedisClient
{
    const DEFAULT_SCHEME = "redis";
    const DEFAULT_HOST = "127.0.0.1";
    const DEFAULT_PORT = 6379;
                         
    /**
     * @var Configuration $config
     */
    private $config;
    
    /**
     * @var ConnectionInterface $connection
     */
    private $connection;

    /**
     * @var Executor $executor
     */
    private $executor;
    
    public function __construct(array $config = [])
    {        
        $this->config = $this->createConfig($config);
        $this->connection = $this->createConnection([
            "debug" => $this->debugEnabled(),
            "scheme" => $this->config->get("scheme", self::DEFAULT_SCHEME),
            "host" => $this->config->get("host", self::DEFAULT_HOST),
            "port" => $this->config->get("port", self::DEFAULT_PORT)
        ]);
        $this->executor = $this->createCommandExecutor();
    }

    /**
     * Create client configuration
     * 
     * @param array $config
     * @return Configuration
     */
    private function createConfig(array $config)
    {
        return new Configuration($config);
    }

    /**
     * Get client configuration option
     * Return default value if option
     * is not defined
     * 
     * @param string $option
     * @param mixed $default
     * @return mixed|false
     */
    private function getConfigOption($option, $default)
    {
        return $this->config->get($option, $default);
    }

    /**
     * Create factory Redis connection
     * 
     * @param array $connectionOptions
     * @return ConnectionInterface 
     */
    private function createConnection(array $configuration): ConnectionInterface
    {
        $scheme = $configuration["scheme"];
        unset($configuration["scheme"]);

        return Factory::create( $scheme, $configuration );
    }

    /**
     * Get client Reddis connection
     * 
     * @return ConnectionInterface 
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Create client command executor
     * 
     * @return Executor
     */
    private function createCommandExecutor(): Executor
    {
        return new Executor($this);
    }

    /**
     * Return if client debug is enaled
     * Default: false
     * 
     * @return bool
     */
    public function debugEnabled(): bool
    {
        return $this->getConfigOption("debug", false);
    }

    /**
     * Execute Redis command
     * 
     * @param string $command
     * @param array $arguments
     */
    public function __call($command, $arguments = [])
    {
        $command = Commands::ACCESSIBLE[$command] ?? null;

        if (is_null($command)) throw new RedisClientException("Top level command '{$command}' not available");

        return $this->executor->addCommandToBuffer( 
            $command, $arguments
        );
    }
}