<?php

namespace Themosis\Finder;

interface IFinder
{
    /**
     * Returns a file path.
     *
     * @param string $name
     *
     * @return string
     */
    public function find($name);

    /**
     * Register a list of paths.
     *
     * @param array $paths
     *
     * @return mixed
     */
    public function addPaths(array $paths);

    /**
     * Return a list of registered paths.
     * 
     * @return array
     */
    public function getPaths();

    /**
     * Return a list of found files.
     *
     * @return array
     */
    public function getFiles();
}
