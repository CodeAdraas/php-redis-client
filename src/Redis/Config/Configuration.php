<?php

namespace Dodo\Redis\Config;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array $config
     */
    private $config = [];

    public function __construct(array $config)
    {
        $this->defineOptions($config);
    }

    /**
     * Define configuration options
     * 
     * @param array $config
     */
    public function defineOptions(array $config): void
    {
        foreach ($config as $option => $value) $this->config[$option] = $value;
    }

    /**
     * Return if configuration option, if defined
     * 
     * @param string $option
     * @return mixed|false
     */
    public function isDefined(string $option)
    {
        return $this->config[$option] ?? false;
    }

    /**
     * Get configuration option
     * 
     * @param string $option
     * @param string|null $default
     * 
     * @return mixed
     */
    public function get(string $option, string $default = null)
    {
        $configOption = $this->isDefined($option);

        return !$configOption ? ($default ?? false) : $configOption;
    }
}