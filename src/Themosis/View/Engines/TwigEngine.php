<?php

namespace Themosis\View\Engines;

use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\ViewFinderInterface;
use Twig_Environment;

class TwigEngine extends PhpEngine
{
    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * @var \Illuminate\View\ViewFinderInterface
     */
    protected $finder;

    /**
     * @var string
     */
    protected $extension = '.twig';

    public function __construct(Twig_Environment $environment, ViewFinderInterface $finder)
    {
        $this->environment = $environment;
        $this->finder = $finder;
    }

    /**
     * Return the evaluated template.
     *
     * @param string $path The file name with its file extension.
     * @param array  $data Template data (view data)
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        $file = array_search($path, $this->finder->getViews());

        // Allow the use of a '.' notation.
        $file = themosis_convert_path($file);

        $template = $this->environment->loadTemplate($file.$this->extension);

        return $template->render($data);
    }
}
