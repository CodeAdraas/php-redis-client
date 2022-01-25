<?php

namespace Dodo\Redis\Command;

class Ex extends BaseCommand implements CommandInterface
{
    private $expireIn;

    public function getId(): string
    {
        return "ex";
    }

    protected function onInvoke(): void
    {
        $this->expireIn = (int) $this->arguments[0] ?? false;
    }

    public function getTTL()
    {
        return $this->expireIn;
    }
}