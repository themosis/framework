<?php

namespace Themosis\Core\Support;

use Symfony\Component\Finder\Finder;

trait IncludesFiles
{
    /**
     * Automatically includes all .php files found on a specified
     * directory path.
     *
     * @param string|array $path
     */
    public function includes($path, string $pattern = '*.php')
    {
        foreach (Finder::create()->files()->name($pattern)->in($path)->sortByName() as $file) {
            /** @var \SplFileInfo $file */
            @include $file->getRealPath();
        }
    }
}
