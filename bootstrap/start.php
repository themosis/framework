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
// Register framework media image size.
/*----------------------------------------------------*/
add_image_size('_themosis_media', 100, 100, true);

add_filter('image_size_names_choose', function($sizes){

    $sizes['_themosis_media'] = __('Themosis Media Thumbnail', THEMOSIS_FRAMEWORK_TEXTDOMAIN);

    return $sizes;
});

/*----------------------------------------------------*/
// Allow developers to add parameters to
// the admin global JS object.
/*----------------------------------------------------*/
add_action('admin_head', function(){

    $datas = apply_filters('themosisAdminGlobalObject', array());

    $output = "<script type=\"text/javascript\">\n\r";
    $output.= "//<![CDATA[\n\r";
    $output.= "var thfmk_themosis = {\n\r";

    if (!empty($datas))
    {
        foreach ($datas as $key => $value)
        {
            $output.= $key.": ".json_encode($value).",\n\r";
        }
    }

    $output.= "};\n\r";
    $output.= "//]]>\n\r";
    $output.= "</script>";

    // Output the datas.
    echo($output);

});

/*----------------------------------------------------*/
// Register framework core assets URL to
// admin global object.
/*----------------------------------------------------*/
add_filter('themosisAdminGlobalObject', function($paths){

    $paths['_themosisAssets'] = themosis_plugin_url().'/src/Themosis/_assets';

    return $paths;

});

/*----------------------------------------------------*/
// Enqueue frameworks assets.
/*----------------------------------------------------*/
// Themosis styles
Themosis\Facades\Asset::add('themosis-core-styles', 'css/_themosis-core.css')->to('admin');

// Themosis scripts
Themosis\Facades\Asset::add('themosis-core-scripts', 'js/_themosis-core.js', array('jquery', 'jquery-ui-sortable', 'underscore', 'backbone', 'mce-view'), false, true)->to('admin');

/*----------------------------------------------------*/
// Bootstrap application.
/*----------------------------------------------------*/
do_action('themosis_bootstrap');