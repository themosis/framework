<?php
namespace Themosis\Configuration;

class ConfigFinder
{
    /**
     * The config directories paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * The list of found configuration files.
     *
     * @var array
     */
    protected $files = [];

    /**
     * The file extensions.
     *
     * @var array
     */
    protected $extensions = ['config.php', 'php'];

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Return the full path to the given filename.
     *
     * @param string $name The file name without the php extension.
     * @return string
     */
    public function find($name)
    {
        if (isset($this->files[$name])) return $this->files[$name];

        return $this->files[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * Get the file path.
     *
     * @param string $name The filename to look after.
     * @param array $paths The registered paths to look at.
     * @throws ConfigException
     * @return string
     */
    protected function findInPaths($name, array $paths)
    {
        foreach ($paths as $path)
        {
            foreach ($this->getPossibleFiles($name) as $file)
            {
                if (file_exists($filePath = $path.$file))
                {
                    return $filePath;
                }
            }
        }

        throw new ConfigException('Configuration "'.$name.'" not found.');
    }

    /**
     * Get a list of available files with given name.
     *
     * @param string $name
     * @return array
     */
    protected function getPossibleFiles($name)
    {
        return array_map(function($extension) use($name)
        {
            return str_replace('.', DS, $name).'.'.$extension;
        }, $this->extensions);
    }
}