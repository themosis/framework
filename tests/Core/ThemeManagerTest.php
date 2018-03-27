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
}
