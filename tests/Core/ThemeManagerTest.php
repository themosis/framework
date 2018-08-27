<?php

namespace Themosis\Tests\Core;

use Composer\Autoload\ClassLoader;
use PHPUnit\Framework\TestCase;
use Theme\Providers\RouteServiceProvider;
use Themosis\Core\Application;
use Themosis\Core\ThemeManager;

class ThemeManagerTest extends TestCase
{
    public function testManagerBootstrapTheme()
    {
        $app = new Application();
        $theme = new ThemeManager($app, $app->themesPath('underscore'), new ClassLoader());
        $theme->load($app->themesPath('underscore/config'));

        $this->assertInstanceOf(RouteServiceProvider::class, $app->getProvider('Theme\Providers\RouteServiceProvider'));
    }

    public function testThemeManagerCanStoreThemeHeaders()
    {
        $app = new Application();
        $theme = new ThemeManager($app, $app->themesPath('underscore'), new ClassLoader());

        $headers = $theme->headers($app->themesPath('underscore/style.css'), $theme->headers);

        $this->assertTrue(is_array($headers));
        $this->assertEquals('_s', $headers['text_domain']);
        $this->assertEquals('1.0.0', $headers['version']);
    }

    public function testThemeManagerSetThemeTextdomainConstant()
    {
        $app = new Application();
        $theme = new ThemeManager($app, $app->themesPath('underscore'), new ClassLoader());

        $theme->load($app->themesPath('underscore/config'));

        $this->assertTrue(defined('THEME_TD'));
        $this->assertEquals('_s', THEME_TD);
    }
}
