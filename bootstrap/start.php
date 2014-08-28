<?php defined('DS') or die('No direct script access.');

/**
 * Bootstrap Themosis framework.
 */
/*----------------------------------------------------*/
// Config extension.
/*----------------------------------------------------*/
defined('CONFIG_EXT') ? CONFIG_EXT : define('CONFIG_EXT', '.config.php');

/*----------------------------------------------------*/
// Set application configurations.
/*----------------------------------------------------*/
do_action('themosis_configurations');

/*----------------------------------------------------*/
// Set the application instance.
/*----------------------------------------------------*/
$app = new Themosis\Core\Application();

/*----------------------------------------------------*/
// Make application available to the facade.
/*----------------------------------------------------*/
Themosis\Facades\Facade::setFacadeApplication($app);

/*----------------------------------------------------*/
// Register framework view paths.
/*----------------------------------------------------*/
add_filter('themosisViewPaths', function($paths){

    $paths[] = themosis_path('sys').'Metabox'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Page'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Field'.DS.'Fields'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Route'.DS.'Views'.DS;

    return $paths;

});

/*----------------------------------------------------*/
// Register framework asset paths.
/*----------------------------------------------------*/
add_filter('themosisAssetPaths', function($paths){

    $coreUrl = themosis_plugin_url().'/src/Themosis/_assets';
    $paths[$coreUrl] = themosis_path('sys').'_assets';

    return $paths;

});

/*----------------------------------------------------*/
// Include helper functions.
/*----------------------------------------------------*/
include_once(themosis_path('sys').'Helpers'.DS.'helpers.php');

/*----------------------------------------------------*/
// Enqueue frameworks assets.
/*----------------------------------------------------*/
// Themosis styles
Themosis\Facades\Asset::add('themosis-core-styles', 'css/_themosis-core.css')->to('admin');

// Themosis scripts
Themosis\Facades\Asset::add('themosis-core-scripts', 'js/_themosis-core.js', array('jquery', 'jquery-ui-sortable', 'underscore', 'backbone'), false, true)->to('admin');

/*----------------------------------------------------*/
// Bootstrap application.
/*----------------------------------------------------*/
do_action('themosis_bootstrap');