<?php
namespace Themosis\Configuration;

class ConfigFinder
{
    /**
     * The config directories paths.
     *
     * @var array
     */
    protected $paths = array();

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }
}