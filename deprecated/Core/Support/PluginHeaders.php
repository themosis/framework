<?php

namespace Themosis\Core\Support;

trait PluginHeaders
{
    /**
     * Plugin file headers.
     *
     * @var array
     */
    public $headers = [
        'name' => 'Plugin Name',
        'plugin_uri' => 'Plugin URI',
        'plugin_prefix' => 'Plugin Prefix',
        'plugin_namespace' => 'Plugin Namespace',
        'plugin_id' => 'Plugin ID',
        'description' => 'Description',
        'version' => 'Version',
        'author' => 'Author',
        'author_uri' => 'Author URI',
        'license' => 'License',
        'license_uri' => 'License URI',
        'text_domain' => 'Text Domain',
        'domain_path' => 'Domain Path',
        'domain_var' => 'Domain Var',
        'network' => 'Network'
    ];
}
