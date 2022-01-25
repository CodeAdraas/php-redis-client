<?php

namespace Dodo\Redis\Command;

class Ping extends BaseCommand implements CommandInterface
{
    private $pong;

    public function getId(): string
    {
        return "ping";
    }

    protected function onInvoke(): void
    {
        $this->pong = $this->arguments[0] ?? "pong";

        $this->commandResolver->addCommandArgument([ "position" => 0, "value" => $this->pong ]);
    }
}