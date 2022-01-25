<?php

namespace Dodo\Redis\Command\Core;

use Dodo\Redis\RedisClient;
use Dodo\Redis\QueryResponse;
use Dodo\Redis\Exception\RedisClientException;

class Executor
{
    /**
     * @var RedisClient $client
     */
    private $client;

    /**
     * @var Resolver $resolver
     */
    private $resolver;

    public function __construct(RedisClient $client) 
    {
        $this->client = $client;
        $this->resolver = $this->createResolver();
    }

    /**
     * Create new command resolver
     * 
     * @return Resolver
     */
    private function createResolver(): Resolver
    {
        return new Resolver();
    }

    /**
     * Add command to the command
     * resolver command buffer
     * 
     * @param string $command
     * @param array $arguments
     * @return RedisClient|QueryResponse
     */
    public function addCommandToBuffer(string $command, array $arguments)
    {
        $isChainableCommand = $this->resolver->addCommandToBuffer(new $command($this->resolver, $arguments));

        return !$isChainableCommand 
            ? $this->executeCommand() // Directly invoke command execution
            : $this->client;          // Return Redis client for command chaining
    }

    /**
     * Flush resolver command buffer
     * and command arguments
     * 
     * @return void
     */
    private function flushResolver(): void
    {
        $this->resolver->flush();
    }

    /**
     * @final
     * @return QueryResponse
     */
    public function executeCommand(): QueryResponse
    {
        $command = $this->resolver->getCommand();
        $arguments = $this->resolver->getSortedCommandArguments();
        $connection = $this->client->getConnection();

        if (!$connection->isOpen()) $connection->open();

        try {
            $result = $connection->redis()->$command(...$arguments);
            $status = true;
        } catch(\RedisException $e) {
            if ($this->client->debugEnabled()) throw new RedisClientException($e->getMessage());
            
            $result = false;
            $status = false;
        }

        $connection->close();

        if ($this->resolver->hasCommand("get") &&
            $this->resolver->isJson()) $result = json_decode($result, false);

        if ($this->resolver->hasCommand("set") &&
            $status) $result = $this->resolver->isJson() ? json_decode($arguments[1], false) : $arguments[1];

        $this->flushResolver();

        return new QueryResponse( $status, null, null, $result, null );
    }
}