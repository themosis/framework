<?php

namespace Themosis\Foundation\Support;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

trait HasConfigurationFiles
{
    protected function getConfigurationFiles(string $path): array
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
            $directory = $this->getNestedDirectory($file, $path);
            $files[$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get configuration file nesting path.
     */
    protected function getNestedDirectory(SplFileInfo $file, string $path): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($path, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }
}
