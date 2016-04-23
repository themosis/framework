<?php

namespace Themosis\Config;

use Themosis\Foundation\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    protected $provides = [
        'config.finder',
        'config'
    ];

    public function register()
    {
        $paths = apply_filters('themosis_config', []);

        $this->getContainer()->add('config.finder', new ConfigFinder($paths));
        $this->getContainer()->add('config', 'Themosis\Config\ConfigFactory')->withArgument('config.finder');
    }
}
