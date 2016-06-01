<?php

namespace Themosis\Load;

/**
 * Common "interface" for extending the WordPress
 * 'functions.php' file.
 */
abstract class Load implements ILoader
{
    /**
     * List of registered paths.
     * 
     * @var array
     */
    protected $paths = [];

    /**
     * List of loaded files.
     *
     * @var array
     */
    protected $files = [];

    /**
     * Loader constructor.
     *
     * @param array $paths Paths to look at (optional).
     */
    public function __construct(array $paths = [])
    {
        $this->paths = $paths;
    }

    /**
     * Add paths to directories where to load files.
     *
     * @param array $paths
     *
     * @return \Themosis\Load\ILoader
     */
    public function add(array $paths)
    {
        $this->paths = $this->paths + $paths;

        return $this;
    }

    /**
     * Load the files.
     */
    public function load()
    {
        foreach ($this->paths as $path) {
            $this->append($path);
        }

        return $this;
    }

    /**
     * Scan the directory at the given path and include
     * all files. Only 1 level iteration.
     *
     * @param string $path The directory path.
     *
     * @return bool
     */
    protected function append($path)
    {
        if (is_dir($path)) {
            $dir = new \DirectoryIterator($path);

            foreach ($dir as $file) {
                if (!$file->isDot() || !$file->isDir()) {
                    $file_extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

                    if ($file_extension === 'php') {
                        $this->files[] = [
                            'name' => $file->getBasename('.php'),
                            'path' => $file->getPath().DS.$file->getBasename(),
                        ];
                    }
                }
            }

            // Organize files per alphabetical order
            // and include them.
            if (!empty($this->files)) {
                usort($this->files, function ($a, $b) {
                    return strnatcmp($a['name'], $b['name']);
                });

                foreach ($this->files as $file) {
                    include_once $file['path'];
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Return a list of loaded files.
     * 
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}
