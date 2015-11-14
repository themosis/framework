<?php defined('DS') or die('No direct script access.');

/**
 * Bootstrap Themosis framework.
 */
/*----------------------------------------------------*/
// Include helper functions.
/*----------------------------------------------------*/
include_once(themosis_path('sys').'Helpers'.DS.'helpers.php');

/*----------------------------------------------------*/
// Set the application instance.
/*----------------------------------------------------*/
if (!class_exists('Themosis\Core\Application'))
{
    // Message for the back-end
    add_action('admin_notices', function()
    {
        ?>
            <div id="message" class="error">
                <p><?php _e(sprintf('<b>Themosis framework:</b> %s', "The autoload.php file is missing or there is a namespace error inside your composer.json file."), THEMOSIS_FRAMEWORK_TEXTDOMAIN); ?></p>
            </div>
        <?php
    });

    // Message for the front-end
    if (!is_admin())
    {
        wp_die(__("The <strong>Themosis framework</strong> is not loaded properly. Please check your <strong>composer.json</strong> file configuration.", THEMOSIS_FRAMEWORK_TEXTDOMAIN));
    }

    return;
}

// Start the project...
$app = new Themosis\Core\Application();

/*----------------------------------------------------*/
// Set the application paths.
/*----------------------------------------------------*/
$app->bindInstallPaths($GLOBALS['themosis_paths']);

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
// Register framework view paths.
/*----------------------------------------------------*/
add_filter('themosisViewPaths', function($paths)
{
    $paths[] = themosis_path('sys').'Metabox'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Page'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'PostType'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Field'.DS.'Fields'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Route'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'User'.DS.'Views'.DS;

    return $paths;
});

/*----------------------------------------------------*/
// Register framework asset paths.
/*----------------------------------------------------*/
add_filter('themosisAssetPaths', function($paths)
{
    $coreUrl = themosis_plugin_url(dirname(__DIR__)).'/src/Themosis/_assets';
    $paths[$coreUrl] = themosis_path('sys').'_assets';

    return $paths;
});

/*----------------------------------------------------*/
// Register framework media image size.
/*----------------------------------------------------*/
$images = new Themosis\Configuration\Images([
    '_themosis_media' => [100, 100, true, __('Mini')]
]);
$images->make();

/*----------------------------------------------------*/
// Allow developers to add parameters to
// the admin global JS object.
/*----------------------------------------------------*/
add_action('admin_head', function()
{
    $datas = apply_filters('themosisAdminGlobalObject', []);

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
add_filter('themosisAdminGlobalObject', function($paths)
{
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
Themosis\Facades\Asset::add('themosis-core-styles', 'css/_themosis-core.css', ['wp-color-picker'])->to('admin');

// Themosis scripts
Themosis\Facades\Asset::add('themosis-core-scripts', 'js/_themosis-core.js', ['jquery', 'jquery-ui-sortable', 'underscore', 'backbone', 'mce-view', 'wp-color-picker'], false, true)->to('admin');

/*----------------------------------------------------*/
// Handle errors, warnings, exceptions.
/*----------------------------------------------------*/
set_exception_handler(function($e)
{
    Themosis\Error\Error::exception($e);
});

set_error_handler(function($code, $error, $file, $line)
{
    // Check if the class exists
    // Otherwise WP can't find it when
    // constructing its "Menus" page
    // under appearance in administration.
    if (class_exists('Themosis\Error\Error'))
    {
        Themosis\Error\Error::native($code, $error, $file, $line);
    }
});

if (defined('THEMOSIS_ERROR_SHUTDOWN') && THEMOSIS_ERROR_SHUTDOWN)
{
    register_shutdown_function(function()
    {
        Themosis\Error\Error::shutdown();
    });
}

// Passing in the value -1 will show every errors.
$report = defined('THEMOSIS_ERROR_REPORT') ? THEMOSIS_ERROR_REPORT : 0;
error_reporting($report);

/*----------------------------------------------------*/
// Set class aliases.
/*----------------------------------------------------*/
$aliases = apply_filters('themosisClassAliases', [
    'Themosis\\Facades\\Action'                 => 'Action',
    'Themosis\\Facades\\Ajax'					=> 'Ajax',
    'Themosis\\Facades\\Asset'					=> 'Asset',
    'Themosis\\Facades\\Config'                 => 'Config',
    'Themosis\\Route\\Controller'               => 'Controller',
    'Themosis\\Facades\\Field'					=> 'Field',
    'Themosis\\Facades\\Form'					=> 'Form',
    'Themosis\\Facades\\Html'                   => 'Html',
    'Themosis\\Facades\\Input'                  => 'Input',
    'Themosis\\Metabox\\Meta'					=> 'Meta',
    'Themosis\\Facades\\Metabox'				=> 'Metabox',
    'Themosis\\Page\\Option'					=> 'Option',
    'Themosis\\Facades\\Page'					=> 'Page',
    'Themosis\\Facades\\PostType'				=> 'PostType',
    'Themosis\\Facades\\Route'					=> 'Route',
    'Themosis\\Facades\\Section'                => 'Section',
    'Themosis\\Session\\Session'				=> 'Session',
    'Themosis\\Taxonomy\\TaxField'              => 'TaxField',
    'Themosis\\Taxonomy\\TaxMeta'               => 'TaxMeta',
    'Themosis\\Facades\\Taxonomy'				=> 'Taxonomy',
    'Themosis\\Facades\\User'					=> 'User',
    'Themosis\\Facades\\Validator'              => 'Validator',
    'Themosis\\Facades\\Loop'					=> 'Loop',
    'Themosis\\Facades\\View'					=> 'View'
]);

foreach ($aliases as $namespace => $className)
{
    class_alias($namespace, $className);
}

/*----------------------------------------------------*/
// Bootstrap plugins.
/*----------------------------------------------------*/
do_action('themosis_bootstrap_plugins', $app);

/*----------------------------------------------------*/
// Bootstrap theme.
/*----------------------------------------------------*/
do_action('themosis_bootstrap_theme', $app);