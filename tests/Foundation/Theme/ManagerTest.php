<?php

namespace Themosis\Tests\Foundation\Theme;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;
use Theme\Models\FakeModel;
use Theme\ThemeHelper;
use Themosis\Asset\AssetException;
use Themosis\Asset\AssetFileInterface;
use Themosis\Asset\AssetServiceProvider;
use Themosis\Asset\Finder;
use Themosis\Foundation\Theme\Manager;
use Themosis\Hook\Filter;
use Themosis\Tests\Installers\WordPressConfiguration;
use Themosis\Tests\TestCase;

class ManagerTest extends TestCase
{
    private Manager $manager;

    private WordPressConfiguration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('view.paths', []);

        $manager = new Manager($this->app, new ClassLoader(), $this->app['config'], new Filter($this->app));
        $manager->load(WP_CONTENT_DIR . '/themes/themosis-fake-theme');

        $this->manager = $manager;

        $this->configuration = $this->app->make(WordPressConfiguration::class);
    }

    /** @test */
    public function it_can_extract_theme_directory_name(): void
    {
        $this->assertEquals($this->configuration->defaultTheme(), $this->manager->getDirectory());
    }

    /** @test */
    public function it_can_load_a_theme_headers(): void
    {
        $this->assertEquals('Themosis Fake Theme', $this->manager->getHeader('name'));
        $this->assertEquals('Themosis', $this->manager->getHeader('author'));
        $this->assertEquals('1.0.0', $this->manager->getHeader('version'));
        $this->assertEquals('fake-domain', $this->manager->getHeader('text_domain'));

        $this->assertNull($this->manager->getHeader('custom_property'));
    }

    /**  @test */
    public function it_can_return_theme_paths(): void
    {
        $path = WP_CONTENT_DIR . '/themes/' . $this->configuration->defaultTheme();

        $this->assertEquals($path, $this->manager->getPath());
        $this->assertEquals($path . '/config', $this->manager->getPath('config'));
    }

    /** @test */
    public function it_can_return_theme_url(): void
    {
        $themePath = 'https://themosis.test/content/themes/' . $this->configuration->defaultTheme();

        $this->assertEquals($themePath, $this->manager->getUrl());
        $this->assertEquals($themePath . '/config/theme.php', $this->manager->getUrl('config/theme.php'));
    }

    /** @test */
    public function it_can_load_theme_configuration(): void
    {
        $config = $this->app['config'];

        $this->assertIsArray($config['theme']);
        $this->assertTrue($config['theme.active']);
        $this->assertIsArray($config['theme.sizes']);
        $this->assertEquals('medium', $config['theme.sizes.md']);
        $this->assertTrue($config['misc.access']);
    }

    /** @test */
    public function it_can_define_theme_textdomain_constant(): void
    {
        $this->assertTrue(defined('THEME_TD'));
        $this->assertEquals(THEME_TD, $this->manager->getHeader('text_domain'));
    }

    /** @test */
    public function it_can_autoload_theme_classes(): void
    {
        $this->assertInstanceOf(ThemeHelper::class, new ThemeHelper());
        $this->assertInstanceOf(FakeModel::class, new FakeModel());
    }

    /** @test */
    public function it_can_register_custom_image_sizes(): void
    {
        $imageSizes = [
            'square' => [250, 250, true],
            'landscape' => [640, 480, false, true],
            'portrait' => [480, 640, false, 'Portrait Image Size'],
        ];

        $this->manager->images($imageSizes);

        $this->assertTrue(has_image_size('square'));
        $this->assertTrue(has_image_size('landscape'));
        $this->assertTrue(has_image_size('portrait'));

        $this->assertEquals(250, wp_get_additional_image_sizes()['square']['width']);
        $this->assertEquals(250, wp_get_additional_image_sizes()['square']['height']);
        $this->assertTrue(wp_get_additional_image_sizes()['square']['crop']);

        $this->assertEquals(640, wp_get_additional_image_sizes()['landscape']['width']);
        $this->assertEquals(480, wp_get_additional_image_sizes()['landscape']['height']);
        $this->assertFalse(wp_get_additional_image_sizes()['landscape']['crop']);

        $this->assertEquals(480, wp_get_additional_image_sizes()['portrait']['width']);
        $this->assertEquals(640, wp_get_additional_image_sizes()['portrait']['height']);
        $this->assertFalse(wp_get_additional_image_sizes()['portrait']['crop']);

        (new Filter($this->app))->add('image_size_names_choose', function (array $sizes) use ($imageSizes) {
            $this->assertTrue(isset($sizes['landscape']));
            $this->assertEquals('Landscape', $sizes['landscape']);

            $this->assertTrue(isset($sizes['portrait']));
            $this->assertEquals($imageSizes['portrait'][3], $sizes['portrait']);
        });

        (new Filter($this->app))->run('image_size_names_choose', []);
    }

    /** @test */
    public function it_can_register_theme_service_providers(): void
    {
        $this->manager->providers([
            TestThemeServiceProvider::class,
            MenuServiceProvider::class,
        ]);

        $this->assertInstanceOf(TestThemeServiceProvider::class, $this->app->getProvider(TestThemeServiceProvider::class));
        $this->assertInstanceOf(MenuServiceProvider::class, $this->app->getProvider(MenuServiceProvider::class));

        $this->assertTrue($this->app->bound('test-theme'));
        $this->assertTrue($this->app->make('test-theme'));
    }

    /** @test */
    public function it_can_register_theme_views(): void
    {
        $this->manager->views([
            'views',
        ]);

        /** @var Factory $factory */
        $factory = $this->app->make('view');

        $this->assertEquals(
            $this->app->basePath('public/content/themes/themosis-fake-theme/views/main.blade.php'),
            $factory->getFinder()->find('main'),
        );
    }

    /** @test */
    public function it_can_locate_theme_assets(): void
    {
        $this->app['config']->set('assets.paths', []);
        $this->app->register(AssetServiceProvider::class);

        $assets = [
            $this->manager->getPath('dist') => $this->manager->getUrl('dist'),
        ];

        $this->manager->assets($assets);

        /** @var Finder $finder */
        $finder = $this->app[Finder::class];

        $this->assertInstanceOf(AssetFileInterface::class, $finder->find('css/theme.css'));
        $this->assertInstanceOf(AssetFileInterface::class, $finder->find('js/app.min.js'));
        $this->assertInstanceOf(AssetFileInterface::class, $finder->find('js/library/fake-library.js'));

        $this->expectException(AssetException::class);
        $finder->find('dir/do/not/exist.js');
    }

    /** @test */
    public function it_can_register_theme_navigation_menus(): void
    {
        $menus = [
            'main' => __('Main Navigation', THEME_TD),
            'secondary' => __('Secondary Navigation', THEME_TD),
        ];

        $this->manager->menus($menus);

        $this->assertEquals($menus, get_registered_nav_menus());
    }

    /** @test */
    public function it_can_register_theme_sidebars(): void
    {
        $sidebars = [
            [
                'name' => __('First sidebar', THEME_TD),
                'id' => 'sidebar-1',
                'description' => __('Area of first sidebar', THEME_TD),
                'class' => 'custom',
                'before_widget' => '<div>',
                'after_widget' => '</div>',
                'before_title' => '<h2>',
                'after_title' => '</h2>',
                'before_sidebar' => '',
                'after_sidebar' => '',
                'show_in_rest' => false,
            ],
        ];

        $this->manager->sidebars($sidebars);

        $this->assertEquals($sidebars[0], wp_get_sidebar('sidebar-1'));
    }

    /** @test */
    public function it_can_register_theme_support(): void
    {
        $supports = [
            'post-thumbnails' => ['post', 'page'],
            'title-tag',
            'custom-feature' => true,
        ];

        $this->manager->support($supports);

        $this->assertEquals($supports['post-thumbnails'], get_theme_support('post-thumbnails')[0]);
        $this->assertFalse(get_theme_support('title-tag'));
        $this->assertTrue(get_theme_support('custom-feature')[0]);
    }

    /** @test */
    public function it_can_register_theme_templates(): void
    {
        $templates = [
            'custom-template' => [__('Custom Template', THEME_TD), ['page']],
            'about-page' => __('About'),
            'video-template' => [__('Video', THEME_TD), 'portfolio'],
        ];

        $this->manager->templates($templates);

        $filter = (new Filter($this->app))->add('theme_page_templates', function ($registered) use ($templates) {
            $this->assertEquals($templates['custom-template'][0], $registered['custom-template']);
            $this->assertEquals($templates['about-page'], $registered['about-page']);
        });

        $filter->run('theme_page_templates', []);

        $filter = (new Filter($this->app))->add('theme_portfolio_templates', function ($registered) use ($templates) {
            $this->assertEquals($templates['video-template'][0], $registered['video-template']);
        });

        $filter->run('theme_portfolio_templates', []);
    }
}

class TestThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('test-theme', function () {
            return true;
        });
    }
}

class MenuServiceProvider extends ServiceProvider
{
}
