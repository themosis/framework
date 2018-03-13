<?php

namespace Themosis\Config;

interface IConfig
{
    /**
     * Get the all or one property from a configuration file.
     *
     * @param string $name The name of the file to look at with its property.
     *
     * @return mixed The properties or one property value.
     */
    public function get($name);
}
