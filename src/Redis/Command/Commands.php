<?php

namespace Dodo\Redis\Command;

class Commands
{
    const ACCESSIBLE = [
        "ping" => "\\Dodo\\Redis\\Command\\Ping",
        "get" => "\\Dodo\\Redis\\Command\\Get",
        "set" => "\\Dodo\\Redis\\Command\\Set",
        "append" => "\\Dodo\\Redis\\Command\\Append",
        "del" => "\\Dodo\\Redis\\Command\\Del",
        "unlink" => "\\Dodo\\Redis\\Command\\Unlink",
        "ex" => "\\Dodo\\Redis\\Command\\Ex",
        /**
         * Support commands
         * > Only changes behaviour of a command
         */
        "ix" => "\\Dodo\\Redis\\Command\\Ix",
        "nx" => "\\Dodo\\Redis\\Command\\Nx",
        "json" => "\\Dodo\\Redis\\Command\\Json",
        "exec" => "\\Dodo\\Redis\\Command\\Exec"
    ];
}