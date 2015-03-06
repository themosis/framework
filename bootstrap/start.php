<?php defined('DS') or die('No direct script access.');

/**
 * Bootstrap Themosis framework.
 */
/*----------------------------------------------------*/
// Config extension.
/*----------------------------------------------------*/
defined('CONFIG_EXT') ? CONFIG_EXT : define('CONFIG_EXT', '.config.php');

/*----------------------------------------------------*/
// Include helper functions.
/*----------------------------------------------------*/
include_once(themosis_path('sys').'Helpers'.DS.'helpers.php');

/*----------------------------------------------------*/
// Set the application instance.
/*----------------------------------------------------*/
$app = new Themosis\Core\Application();

/*----------------------------------------------------*/
// Set the application paths.
/*----------------------------------------------------*/
$paths = apply_filters('themosis_application_paths', array(
    'plugin'    => dirname(__DIR__),
    'sys'       => dirname(__DIR__).DS.'src'.DS.'Themosis'.DS
));

$app->bindInstallPaths($paths);

/*----------------------------------------------------*/
// Bind the application in the container.
/*----------------------------------------------------*/
$app->instance('app', $app);

/*----------------------------------------------------*/
// Load the facades.
/*----------------------------------------------------*/
Themosis\Facades\Facade::clearResolvedInstances();

Themosis\Facades\Facade::setFacadeApplication($app);

/*----------------------------------------------------*/
// Register Facade Aliases To Full Classes
/*----------------------------------------------------*/
$app->registerCoreContainerAliases();

/*----------------------------------------------------*/
// Register Core Igniter services
/*----------------------------------------------------*/
$app->registerCoreIgniters();

/*----------------------------------------------------*/
// Set application configurations.
/*----------------------------------------------------*/
do_action('themosis_configurations');

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

    $coreUrl = themosis_plugin_url(dirname(__DIR__)).'/src/Themosis/_assets';
    $paths[$coreUrl] = themosis_path('sys').'_assets';

    return $paths;

});


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

    $paths['_themosisAssets'] = themosis_plugin_url(dirname(__DIR__)).'/src/Themosis/_assets';

    return $paths;

});

/*----------------------------------------------------*/
// Load the WordPress Media API assets by default.
// Pass a function name so developers can remove the
// default action if necessary.
/*----------------------------------------------------*/
function themosisWpMediaAssets()
{
    wp_enqueue_media();
}

add_action('admin_enqueue_scripts', 'themosisWpMediaAssets');

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