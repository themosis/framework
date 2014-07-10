<?php
namespace Themosis\Configuration;

use Symfony\Component\ClassLoader\MapClassLoader;

class Models {

    /**
     * Save the retrieved datas.
     *
     * @var array
     */
    private $mapping;

    /**
     * Set the model mapping for autoloading.
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