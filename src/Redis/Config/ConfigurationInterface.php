<?php

namespace Dodo\Redis\Config;

interface ConfigurationInterface
{
    public function __construct(array $config);

    /**
     * Define configuration options
     * 
     * @param array $config
     */
    public function defineOptions(array $config): void;

    /**
     * Return if configuration option, if defined
     * 
     * @param string $option
     * @return mixed|false
     */
    public function isDefined(string $option);

    /**
     * Get configuration option
     * 
     * @param string $option
     * @param string|null $default
     * 
     * @return mixed
     */
    public function get(string $option, string $default = null);
}