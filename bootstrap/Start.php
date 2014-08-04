<?php defined('DS') or die('No direct script access.');

/**
 * Handle Core framework components
 * Used to bootstrap Themosis.
*/
/*----------------------------------------------------
| Themosis core constants
|
|---------------------------------------------------*/
defined('CONFIG_EXT') ? CONFIG_EXT : define('CONFIG_EXT', '.config.php');

/*----------------------------------------------------
| Trigger for configurations
|
|---------------------------------------------------*/
do_action('themosis_configurations');

/*----------------------------------------------------
| Create the application instance.
|
|---------------------------------------------------*/
$app = new Themosis\Core\Application();

/*----------------------------------------------------
| Make the application available to the facade.
|
|---------------------------------------------------*/
Themosis\Facades\Facade::setFacadeApplication($app);

/*----------------------------------------------------
| Register core view paths.
|
|---------------------------------------------------*/
add_filter('themosisViewPaths', function($paths){

    $paths[] = themosis_path('sys').'Metabox'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Page'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Field'.DS.'Fields'.DS.'Views'.DS;

    return $paths;

});

/*----------------------------------------------------
| Register core asset paths.
|
|---------------------------------------------------*/
add_filter('themosisAssetPaths', function($paths){

    // Core paths.
    $coreUrl = themosis_plugin_url().'/src/Themosis/_assets';
    $paths[$coreUrl] = themosis_path('sys').'_assets';

    return $paths;

});

/*----------------------------------------------------
| Add global helpers functions
|
|---------------------------------------------------*/
include_once(themosis_path('sys').'Helpers'.DS.'helpers.php');

/*----------------------------------------------------
| Handle core frameworks assets
|
|---------------------------------------------------*/
// Themosis custom styles
Themosis\Facades\Asset::add('themosis-core-styles', 'css/_themosis-core.css')->to('admin');

// Themosis custom scripts
Themosis\Facades\Asset::add('themosis-core-scripts', 'js/_themosis-core.js', array('jquery', 'jquery-ui-sortable', 'underscore', 'backbone'), false, true)->to('admin');

// ---------------------------------------------------
// Bootstrap application.
// ---------------------------------------------------
do_action('themosis_bootstrap');