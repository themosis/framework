<?php

namespace Themosis\Load;

interface ILoader
{
    /**
     * Add paths to directories where files need "autoloading".
     *
     * @param array $paths
     */
    public function add(array $paths);

    /**
     * Load the files.
     *
     * @return mixed
     */
    public function load();

    /**
     * Return a list of loaded files.
     * 
     * @return array
     */
    public function getFiles();
}
