<?php

namespace Dodo\Redis\Command\Core;

use Dodo\Redis\Command\CommandInterface;

class Resolver
{
    /**
     * @var Buffer $buffer
     */
    private $buffer;
    
    /**
     * @var Arguments $arguments
     */
    private $arguments;

    public function __construct()
    {
        $this->buffer = $this->createCommandBuffer();
        $this->arguments = $this->createCommandArguments();
    }

    /**
     * Create new command buffer
     * 
     * @return Buffer
     */
    private function createCommandBuffer(): Buffer
    {
        return new Buffer();
    }

    /**
     * Create new command buffer and
     * return a boolean indicating if 
     * command is chainable
     * 
     * @param CommandInterface $command
     * @return bool
     */
    public function addCommandToBuffer(CommandInterface $command): bool
    {
        $commandId = $this->buffer->add($command)->getId();
        
        // Is command chainable?
        return ($commandId === "ping" ||
                $commandId === "exec" ||
                $commandId === "del"  ||
                $commandId === "unlink") ? false : true;
    }

    /**
     * Flush all commands in buffer
     * 
     * @return void
     */
    private function flushCommandBuffer(): void
    {
        $this->buffer->flush();
    }

    /**
     * Create new command arguments
     * These arguments will be used in the final
     * command execution stage
     * 
     * @return Arguments
     */
    private function createCommandArguments(): Arguments
    {
        return new Arguments();
    }

    /**
     * Add argument to command arguments
     * 
     * @param array $options
     * @return mixed The added argument
     */
    public function addCommandArgument(array $options)
    {
        return $this->arguments->add($options);
    }

    /**
     * Get current unsorted command
     * arguments
     * 
     * @return array
     */
    public function getCommandArguments(): array
    {
        return $this->arguments->get();
    }

    /**
     * Get current sorted command
     * arguments
     * 
     * @return array
     */
    public function getSortedCommandArguments(): array
    {
        return $this->arguments->getSorted();
    }

    /**
     * Flush all commands arguments
     * 
     * @return void
     */
    private function flushCommandArguments(): void
    {
        $this->arguments->flush();
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->flushCommandBuffer();
        $this->flushCommandArguments();
    }

    /**
     * Check if specific command is
     * inside current command buffer
     * 
     * @return CommandInterface|null
     */
    public function hasCommand(string $commandId)
    {
        return $this->buffer->contains($commandId);
    }

    /**
     * @return CommandInterface|null
     */
    public function ifExists()
    {
        return $this->hasCommand("ix");
    }

    /**
     * @return CommandInterface|null
     */
    public function ifNotExists()
    {
        return $this->hasCommand("nx");
    }

    /**
     * @return CommandInterface|null
     */
    public function isJson()
    {
        return $this->hasCommand("json");
    }

    /**
     * @return CommandInterface|null
     */
    public function hasExpire()
    {
        return $this->hasCommand("ex");
    }

    /**
     * Get the correct command ID needed
     * for final command execution
     * 
     * Sort correct arguments needed
     * for final command execution
     * 
     * @return string|null Command ID
     */
    public function getCommand()
    {
        if ($this->hasCommand("set") && 
            $this->isJson()) $this->addCommandArgument([
                "position" => 1,
                "value" => json_encode($this->getSortedCommandArguments()[1])
            ]);

        $options = [];
        if ($this->hasCommand("set") && 
            $this->ifExists()) $options[0] = "xx";

        if ($this->hasCommand("set") && 
            $this->ifNotExists()) $options[0] = "nx";

        if ($this->hasCommand("set") && 
            $ex = $this->hasExpire()) $options["ex"] = $ex->getTTL();

        if ($this->hasCommand("set") &&
            !empty($options)) $this->addCommandArgument([
                "position" => 2, 
                "value" => $options 
            ]);

        $command = $this->hasCommand("ping") ??
                   $this->hasCommand("get") ?? 
                   $this->hasCommand("set") ??
                   $this->hasCommand("append") ??
                   $this->hasCommand("del") ??
                   $this->hasCommand("unlink") ??
                   null;

        return $command->getId() ?? null;
    }
}