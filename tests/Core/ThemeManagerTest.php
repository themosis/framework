<?php

namespace Themosis\Tests\Core;

use Composer\Autoload\ClassLoader;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;
use Theme\Providers\RouteServiceProvider;
use Themosis\Core\Application;
use Themosis\Core\ThemeManager;
use Themosis\Hook\FilterBuilder;

class ThemeManagerTest extends TestCase
{
    protected $app;

    protected function getApplication()
    {
        if (! is_null($this->app)) {
            return $this->app;
        }

        $app = new Application();

        $app->singleton('config', function () {
            return new Repository();
        });

        $app->bind('filter', function ($app) {
            return new FilterBuilder($app);
        });

        return $this->app = $app;
    }

    protected function getThemeManager()
    {
        $app = $this->getApplication();

        return new ThemeManager($app, $app->themesPath('underscore'), new ClassLoader());
    }

    public function testManagerBootstrapTheme()
    {
        $app = $this->getApplication();
        $theme = $this->getThemeManager();
        $theme->load($app->themesPath('underscore/config'));
        $theme->providers($theme->config('theme.providers'));

        $this->assertInstanceOf(RouteServiceProvider::class, $app->getProvider('Theme\Providers\RouteServiceProvider'));
    }

    public function testThemeManagerCanStoreThemeHeaders()
    {
        $app = $this->getApplication();
        $theme = $this->getThemeManager();

        $headers = $theme->headers($app->themesPath('underscore/style.css'), $theme->headers);

        $this->assertTrue(is_array($headers));
        $this->assertEquals('_s', $headers['text_domain']);
        $this->assertEquals('1.0.0', $headers['version']);
    }

    public function testThemeManagerSetThemeTextdomainConstant()
    {
        $app = $this->getApplication();
        $theme = $this->getThemeManager();

        $theme->load($app->themesPath('underscore/config'));

        $this->assertTrue(defined('THEME_TD'));
        $this->assertEquals('_s', THEME_TD);
    }

    public function testThemeManagerRegisterImageSizes()
    {
        $app = $this->getApplication();
        $theme = $this->getThemeManager();
        $theme->load($app->themesPath('underscore/config'));

        $theme->images($theme->config('images'));

        $sizes = $theme->images->getSizes();

        $this->assertEquals(6, count($sizes));
        $this->assertEquals([
            'width' => 50,
            'height' => 50,
            'crop' => false,
            'label' => false
        ], $sizes['square']);

        $this->assertEquals([
            'width' => 200,
            'height' => 200,
            'crop' => true,
            'label' => 'Working Sample'
        ], $sizes['working-sample']);

        $this->assertEquals([
            'width' => 200,
            'height' => 200,
            'crop' => false,
            'label' => false
        ], $sizes['no-dropdown']);
    }

    public function testThemeManagerSetTheThemeDirectoryProperty()
    {
        $app = $this->getApplication();
        $theme = $this->getThemeManager();
        $theme = $theme->load($app->themesPath('underscore/config'));

        $this->assertEquals('underscore', $theme->getDirectory());
    }

    public function testThemeManagerCanAutoIncludesFiles()
    {
        $app = $this->getApplication();
        $theme = $this->getThemeManager();
        $theme = $theme->load($app->themesPath('underscore/config'));

        $theme->includes(__DIR__.'/../samples/inc');

        $this->assertTrue(defined('THEME_MANAGER_INC'));
        $this->assertTrue(defined('THEME_MANAGER_NESTED_INC'));
    }
}
