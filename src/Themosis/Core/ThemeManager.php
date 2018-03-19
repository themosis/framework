<?php

namespace Themosis\Core;

use Themosis\Hook\IHook;

class ThemeManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $dirPath;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var \WP_Theme
     */
    protected $theme;

    /**
     * @var string
     */
    protected $routesPath;

    public function __construct(Application $app, string $dirPath, IHook $action)
    {
        $this->app = $app;
        $this->dirPath = $dirPath;
        $this->action = $action;

        $this->init();
    }

    /**
     * Initialize WordPress theme.
     */
    protected function init()
    {
        $name = ltrim(str_replace($this->app->themesPath(), '', $this->dirPath), '\/');
        $this->theme = new \WP_Theme($name, $this->app->themesPath());
    }

    /**
     * Load the theme. Setup theme requirements.
     *
     * @param string $routesPath The relative routes.php file path based on theme root folder.
     *
     * @return $this
     */
    public function load(string $routesPath): ThemeManager
    {
        $this->routesPath = $this->app->themesPath($this->theme->get_stylesheet().'/'.ltrim($routesPath, '\/'));

        $this->action->add('template_redirect', [$this, 'loadThemeRoutes']);

        return $this;
    }

    /**
     * Load theme "routes.php" file.
     */
    public function loadThemeRoutes()
    {
        require $this->getThemeRoutesPath();
    }

    /**
     * Return the path to the theme routes.php file.
     *
     * @return string
     */
    public function getThemeRoutesPath(): string
    {
        return $this->routesPath;
    }
}
