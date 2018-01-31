<?php

if (!function_exists('themosis_set_paths')) {
    /**
     * Register paths globally.
     *
     * @param array $paths Paths to register using alias => path key value pairs.
     */
    function themosis_set_paths(array $paths)
    {
        foreach ($paths as $name => $path) {
            if (!isset($GLOBALS['themosis.paths'][$name])) {
                $GLOBALS['themosis.paths'][$name] = realpath($path).DS;
            }
        }
    }
}

if (!function_exists('themosis_path')) {
    /**
     * Helper function to retrieve a previously registered path.
     *
     * @param string $name The path name/alias. If none is provided, returns all registered paths.
     *
     * @return string|array
     */
    function themosis_path($name = '')
    {
        if (!empty($name)) {
            return $GLOBALS['themosis.paths'][$name];
        }

        return $GLOBALS['themosis.paths'];
    }
}
