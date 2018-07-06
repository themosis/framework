<?php

namespace Themosis\Page;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Themosis\Support\Contracts\UIContainerInterface;

class PageView implements UIContainerInterface
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * The page view path.
     *
     * @var string
     */
    protected $view = '';

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var View
     */
    protected $viewInstance;

    /**
     * @var bool
     */
    protected $useShortViewPath = false;

    /**
     * Page view theme (namespace).
     *
     * @var string
     */
    protected $theme = 'themosis';

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Return the page view theme.
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Set the page view theme.
     *
     * @param string $theme
     *
     * @return UIContainerInterface
     */
    public function setTheme(string $theme): UIContainerInterface
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Return the page view layout.
     *
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * Set the page view layout.
     *
     * @param string $layout
     *
     * @return UIContainerInterface
     */
    public function setLayout(string $layout): UIContainerInterface
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Set the page view.
     *
     * @param string $view
     *
     * @return UIContainerInterface
     */
    public function setView(string $view): UIContainerInterface
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set the page view instance.
     *
     * @param Renderable $view
     *
     * @return UIContainerInterface
     */
    public function setViewInstance(Renderable $view): UIContainerInterface
    {
        $this->viewInstance = $view;

        return $this;
    }

    /**
     * Return the page view path.
     *
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->view;
    }

    /**
     * Return the page view.
     *
     * @return View
     */
    public function getView(): View
    {
        $path = $this->useShortViewPath ?
            $this->getViewPath() : sprintf('%s.%s.%s', $this->getTheme(), $this->getLayout(), $this->getViewPath());

        if (is_null($this->viewInstance)) {
            $this->viewInstance = $this->factory->make($path);
        }

        return $this->viewInstance;
    }

    /**
     * Return the page view factory.
     *
     * @return Factory
     */
    public function factory(): Factory
    {
        return $this->factory;
    }

    /**
     * Tell the page UI to use the view path only instead
     * of the composed one with page layout and theme.
     *
     * @param bool $use
     *
     * @return $this
     */
    public function useShortPath(bool $use = true): UIContainerInterface
    {
        $this->useShortViewPath = $use;

        return $this;
    }
}
