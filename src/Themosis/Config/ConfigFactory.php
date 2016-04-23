<?php

namespace Themosis\Config;

class ConfigFactory implements IConfig
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
     *
     * @return mixed
     */
    public function get($name)
    {
        if (strpos($name, '.') !== false) {
            list($name, $property) = explode('.', $name);
        }

        $path = $this->finder->find($name);
        $properties = include $path;

        // Looking for single property
        if (isset($property) && isset($properties[$property])) {
            return $properties[$property];
        }

        return $properties;
    }
}
