<?php

namespace Dodo\Redis\Command;

interface CommandInterface
{
    /**
     * Return command ID of
     * certain command
     * 
     * @return string Command ID
     */
    public function getId(): string;
}