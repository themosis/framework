<?php

namespace Themosis\Config;

use Themosis\Finder\Finder;

class ConfigFinder extends Finder
{
    /**
     * The file extensions.
     *
     * @var array
     */
    protected $extensions = ['config.php', 'php'];
}
