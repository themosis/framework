<?php

namespace Themosis\Foundation\Support;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

trait IncludesFiles
{
    /**
     * Automatically includes all .php files, in alphabetical order, found on a specified
     * directory path.
     */
    public function includes(string|array $path, string $pattern = '*.php'): void
    {
        foreach (Finder::create()->files()->name($pattern)->in($path)->sortByName() as $file) {
            /** @var SplFileInfo $file */
            @include $file->getRealPath();
        }
    }
}
