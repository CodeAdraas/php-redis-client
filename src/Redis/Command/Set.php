<?php

namespace Dodo\Redis\Command;

class Set extends BaseCommand implements CommandInterface
{
    private $key;

    private $value;

    public function getId(): string
    {
        return "set";
    }

    protected function onInvoke(): void
    {
        $this->key = $this->arguments[0] ?? false;
        $this->value = $this->arguments[1] ?? false;

        $this->commandResolver->addCommandArgument([ "position" => 0, "value" => (string) $this->key ]);
        $this->commandResolver->addCommandArgument([ "position" => 1, "value" => $this->value ]);
    }
}