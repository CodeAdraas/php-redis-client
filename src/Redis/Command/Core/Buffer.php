<?php

namespace Dodo\Redis\Command\Core;

use Dodo\Redis\Command\CommandInterface;

class Buffer
{
    /**
     * @var array $buffer
     */
    private $buffer = [];

    /**
     * Add command to buffer
     * 
     * @return CommandInterface
     */
    public function add(CommandInterface $command): CommandInterface
    {
        return $this->buffer[] = $command;
    }

    /**
     * Check if specific command is
     * inside current command buffer
     * 
     * @return CommandInterface|null
     */
    public function contains(string $commandId)
    {
        foreach ($this->buffer as $command)
            if ($command->getId() === $commandId)
                return $command;

        return null;
    }

    /**
     * Flush arguments
     * 
     * @return void
     */
    public function flush(): void
    {
        foreach ($this->buffer as $command) unset($command);
        $this->buffer = [];
    }
}