<?php

namespace Themosis\View\Engines;

use Illuminate\Contracts\View\Engine;
use Themosis\View\FileViewFinder;

class Twig implements Engine
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var FileViewFinder
     */
    protected $finder;

    /**
     * @var string
     */
    protected $extension = '.twig';

    public function __construct(\Twig_Environment $twig, FileViewFinder $finder)
    {
        $this->twig = $twig;
        $this->finder = $finder;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path The file name with its file extension.
     * @param array  $data View data (context)
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        $name = array_search($path, $this->finder->getViews());

        return $this->twig->render($this->parseFileName($name).$this->extension, $data);
    }

    /**
     * Parse the view file name. Replace "." by "/" characters.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseFileName(string $name)
    {
        return str_replace('.', '/', $name);
    }
}
