<?php

namespace Themosis\Tests\Core;

use Com\Themosis\Plugin\Providers\Route;
use Composer\Autoload\ClassLoader;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\PluginManager;

class PluginManagerTest extends TestCase
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

        return $this->app = $app;
    }

    protected function getPlugin()
    {
        $app = $this->getApplication();

        return (new PluginManager($app, $app->pluginsPath('timeline'.DIRECTORY_SEPARATOR.'timeline.php'), new ClassLoader()))->load('config');
    }

    public function testManagerBootstrapPlugin()
    {
        $plugin = $this->getPlugin();

        $this->assertEquals('timeline', $plugin->getDirectory());
        $this->assertEquals($this->getApplication()->pluginsPath('timeline'), $plugin->getPath());
        $this->assertTrue(defined('TIMELINE_TD'));

        $this->assertEquals($this->getApplication()->pluginsPath('timeline'.DIRECTORY_SEPARATOR.'dist'), $plugin->getPath('dist'));
    }

    public function testPluginHeaders()
    {
        $plugin = $this->getPlugin();

        $this->assertEquals('Timeline', $plugin->getHeader('name'));
        $this->assertEquals('TIMELINE_TD', strtoupper($plugin->getHeader('domain_var')));
        $this->assertEquals('1.0.5', $plugin->getHeader('version'));
        $this->assertEquals('namespace', $plugin->getHeader('plugin_prefix'));
    }

    public function testPluginConfiguration()
    {
        $plugin = $this->getPlugin();

        $this->assertEquals([
            'Com\\Themosis\\Plugin\\' => 'resources',
        ], $plugin->config('plugin.autoloading'));

        $this->assertTrue($plugin->config('plugin.anyvar'));
        $this->assertEquals(['views'], $plugin->config('plugin.views'));
    }

    public function testPluginRegisterServicesProviders()
    {
        $app = $this->getApplication();
        $plugin = $this->getPlugin();
        $plugin->providers($plugin->config('plugin.providers'));

        $this->assertInstanceOf(Route::class, $app->getProvider('Com\Themosis\Plugin\Providers\Route'));
    }
}
