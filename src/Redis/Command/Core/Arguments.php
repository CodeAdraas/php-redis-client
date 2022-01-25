<?php

namespace Dodo\Redis\Command\Core;

class Arguments 
{
    /**
     * @var array $arguments
     */
    private $arguments = [];

    /**
     * Add argument
     * 
     * @return mixed
     */
    public function add($options)
    {
        $position = $options["position"];
        $value = $options["value"];

        return $this->arguments[$position] = $value;
    }

    /**
     * Get unsorted array of arguments
     * 
     * @return array
     */
    public function get(): array
    {
        return array_values($this->arguments);
    }

    /**
     * Get sorted array of arguments
     * 
     * @return array
     */
    public function getSorted(): array
    {
        $arguments = $this->arguments;
        ksort($arguments, SORT_NUMERIC);

        return array_values($arguments);
    }

    /**
     * Flush arguments
     * 
     * @return void
     */
    public function flush(): void
    {
        $this->arguments = [];
    }
}