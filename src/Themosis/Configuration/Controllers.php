<?php
namespace Themosis\Configuration;

use Symfony\Component\ClassLoader\MapClassLoader;

class Controllers {

    /**
     * Save the retrieved datas.
     *
     * @var array
     */
    private $mapping;

    /**
     * Set controller mapping for autoloading.
     *
     * @param string $path Path to the config file.
     */
    public function set($path)
    {
        $this->mapping = include($path);

        // Register mapping
        $loader = new MapClassLoader($this->mapping);
        $loader->register();
    }

} 