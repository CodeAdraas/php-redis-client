<?php

namespace Dodo\Redis\Command;

class Unlink extends BaseCommand implements CommandInterface
{
    private $key;

    public function getId(): string
    {
        return "unlink";
    }

    protected function onInvoke(): void
    {
        $this->key = $this->arguments[0] ?? false;

        $this->commandResolver->addCommandArgument([ "position" => 0, "value" => $this->key ]);
    }
}