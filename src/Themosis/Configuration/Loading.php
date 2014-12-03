<?php
namespace Themosis\Configuration;

use Symfony\Component\ClassLoader\MapClassLoader;

class Loading
{
    /**
     * Set the auto-loading of classes registered inside
     * the loading.config.php file.
     *
     * @param string $path The path to the loading.config.php file
     * @return void
     */
    public function set($path)
    {
        $mapping = include($path);

        // Register mapping
        $loader = new MapClassLoader($mapping);
        $loader->register();
    }
}
