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
}
