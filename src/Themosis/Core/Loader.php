<?php
namespace Themosis\Core;

/**
 * Common "interface" for extending the WordPress
 * 'functions.php' file.
 */
abstract class Loader
{
    /**
     * Keep a copy of file names.
     */
    protected $names = [];

    /**
     * Scan the directory at the given path and include
     * all files. Only 1 level iteration.
     *
     * @param string $path The directory/file path.
     * @return bool True. False if not appended.
     */
    protected function append($path)
    {
        $files = [];

        if (is_dir($path))
        {
            $dir = new \DirectoryIterator($path);

            foreach ($dir as $file)
            {
                if (!$file->isDot() || !$file->isDir())
                {
                    $file_extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

                    if ($file_extension === 'php')
                    {
                        $this->names[] = $file->getBasename('.php');
                        $files[] = [
                            'name' => $file->getBasename('.php'),
                            'path' => $file->getPath().DS.$file->getBasename()
                        ];
                    }
                }
            }

            // Organize files per alphabetical order
            // and include them.
            if (!empty($files))
            {
                usort($files, function($a, $b)
                {
                    return strnatcmp($a['name'],$b['name']);
                });

                foreach ($files as $file)
                {
                    include_once($file['path']);
                }
            }

            return true;
        }

        return false;
    }
}