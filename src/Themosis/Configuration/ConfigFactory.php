<?php
namespace Themosis\Configuration;

class ConfigFactory
{
    /**
     * Config file finder instance.
     *
     * @var ConfigFinder
     */
    protected $finder;

    public function __construct(ConfigFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Return all or specific property from a config file.
     *
     * @param string $name The config file name or its property full name.
     * @return \Themosis\Configuration\IConfig
     */
    public function get($name)
    {

    }
}