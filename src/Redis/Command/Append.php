<?php

namespace Dodo\Redis\Command;

class Append extends BaseCommand implements CommandInterface
{
    private $key;
    private $append;

    public function getId(): string
    {
        return "append";
    }

    protected function onInvoke(): void
    {
        $this->key = $this->arguments[0] ?? false;
        $this->append = $this->arguments[1] ?? false;

        $this->commandResolver->addCommandArgument([ "position" => 0, "value" => $this->key ]);
        $this->commandResolver->addCommandArgument([ "position" => 1, "value" => $this->append ]);
    }
}