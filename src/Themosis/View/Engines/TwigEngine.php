<?php

namespace Themosis\View\Engines;

use Themosis\Finder\IFinder;
use Twig_Environment;

class TwigEngine implements IEngine
{
    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * @var IFinder
     */
    protected $finder;

    /**
     * @var string
     */
    protected $extension = '.twig';

    public function __construct(Twig_Environment $environment, IFinder $finder)
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
        $file = array_search($path, $this->finder->getFiles());

        $template = $this->environment->loadTemplate($file.$this->extension);

        return $template->render($data);
    }
}
