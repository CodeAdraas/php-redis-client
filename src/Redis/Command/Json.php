<?php

namespace Dodo\Redis\Command;

/**
 * Support commands like this one only change behaviour of a 
 * command thus not needing an 'onInvoke' method and in it's
 * generality, a link to Dodo\Redis\Command\BaseCommand
 */
class Json implements CommandInterface
{
    public function getId(): string
    {
        return "json";
    }
}