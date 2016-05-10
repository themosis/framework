<?php

namespace Themosis\View;

use Themosis\Finder\Finder;

class ViewFinder extends Finder
{
    /**
     * The view file extensions.
     *
     * @var array
     */
    protected $extensions = ['scout.php', 'php', 'twig'];
}
