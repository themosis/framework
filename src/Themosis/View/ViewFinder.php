<?php
namespace Themosis\View;

class ViewFinder {

    /**
     * View directories paths.
     *
     * @var array
     */
    protected $paths;

    /**
     * List of found views.
     * $key is the view name.
     * $value is the view path.
     *
     * @var array
     */
    protected $views = [];

    /**
     * The view file extensions.
     *
     * @var array
     */
    protected $extensions = ['scout.php', 'php'];

    /**
     * Build a ViewFinder instance.
     *
     * @param array $paths List of view directories paths.
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Get the real path of a view.
     *
     * @param string $name
     * @return string
     */
    public function find($name)
    {
        if (isset($this->views[$name])) return $this->views[$name];

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * Look for a view in all registered paths.
     *
     * @param string $name
     * @param array $paths
     * @throws ViewException
     * @return string
     */
    protected function findInPaths($name, array $paths)
    {
        foreach ($paths as $path)
        {
            foreach ($this->getPossibleViewFiles($name) as $file)
            {
                if (file_exists($viewPath = $path.$file))
                {
                    return $viewPath;
                }
            }
        }

        throw new ViewException('View "'.$name.'" not found.');

    }

    /**
     * Give a list of possible view file names.
     *
     * @param string $name
     * @return array
     */
    protected function getPossibleViewFiles($name)
    {
        return array_map(function($extension) use($name)
        {
            return str_replace('.', DS, $name).'.'.$extension;
        }, $this->extensions);
    }

} 