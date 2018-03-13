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
        $segments = explode('.', $name);

        $path = $this->finder->find($segments[0]);
        $properties = include $path;

        // When you are just checking
        // if the configuration file exists
        if(count($segments) === 1) return $properties;
        // Remove the file_name segment
        // we are already in the file
        array_splice($segments, 0, 1);

        foreach ($segments as $segment) {

            if (is_array($properties) && array_key_exists($segment, $properties)) {
                $properties = $properties[$segment];
            }else{
                throw new ConfigException('Property "'.$segment.'" not found.');
            }
        }

        return $properties;

    }


    /**
     * Check all or specific property from a config file exists.
     *
     * @param string $name The config file name or its property full name.
     *
     * @return mixed
     */
    public function has($name)
    {
        $segments = explode('.', $name);

        $path = $this->finder->find($segments[0]);
        $properties = include $path;

        // When you are just checking
        // if the configuration file exists
        if(count($segments) === 1) return true;

        // Remove the file_name segment
        // we are already in the file
        array_splice($segments, 0, 1);

        foreach ($segments as $segment) {

            if (is_array($properties) && array_key_exists($segment, $properties)) {
                $properties = $properties[$segment];
            } else {
                return false;
            }
        }
        return true;

    }

}
