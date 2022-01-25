<?php

namespace Dodo\Redis\Command;

use Dodo\Redis\Command\Core\Resolver;

abstract class BaseCommand
{
    /**
     * Default method name of invoking
     * a command to do it's processing
     */
    protected const ON_INVOKE_METHOD = "onInvoke";

    /**
     * The arguments that where passed 
     * via the client
     * 
     * @var array $arguments
     */
    protected $arguments = [];

    /**
     * @var Resolver
     */
    protected $commandResolver;

    public function __construct(Resolver $commandResolver, array $arguments = [])
    {
        $this->commandResolver = $commandResolver;
        $this->arguments = $arguments;

        if (method_exists($this, self::ON_INVOKE_METHOD)) $this->{self::ON_INVOKE_METHOD}();
    }

    /**
     * Invoke method that let's a command
     * do it's internal processing
     * 
     * @return void
     */
    abstract protected function onInvoke(): void;
}