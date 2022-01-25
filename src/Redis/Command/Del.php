<?php

namespace Dodo\Redis\Command;

class Del extends BaseCommand implements CommandInterface
{
    private $key;

    public function getId(): string
    {
        return "del";
    }

    protected function onInvoke(): void
    {
        $this->key = $this->arguments[0] ?? false;

        $this->commandResolver->addCommandArgument([ "position" => 0, "value" => $this->key ]);
    }
}