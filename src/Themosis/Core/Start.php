<?php defined('DS') or die('No direct script access.');

/**
 * Handle Core framework components
 * Used to bootstrap Themosis.
*/
/*----------------------------------------------------
| Themosis core constants
|
|---------------------------------------------------*/
defined('EXT') ? EXT : define('EXT', '.php');
defined('SCOUT_EXT') ? SCOUT_EXT : define('SCOUT_EXT', '.scout.php');
defined('CONFIG_EXT') ? CONFIG_EXT : define('CONFIG_EXT', '.config.php');
defined('CONTROLLER_EXT') ? CONTROLLER_EXT : define('CONTROLLER_EXT', '.controller.php');
defined('MODEL_EXT') ? MODEL_EXT : define('MODEL_EXT', '.model.php');

/*----------------------------------------------------
| Setup configurations - For the datas
|
|---------------------------------------------------*/
$configs = array(
    'datas' => array(
        'application',
        'constants',
        'errors',
        'images'
    )
);

Themosis\Configuration\Config::make($configs);
Themosis\Configuration\Config::set();

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
| Set application classes' alias
|
|---------------------------------------------------*/
foreach (Themosis\Configuration\Application::get('aliases') as $namespace => $className){
	class_alias($namespace, $className);
}

/*----------------------------------------------------
| Themosis textdomain
|
|---------------------------------------------------*/
defined('THEMOSIS_TEXTDOMAIN') ? THEMOSIS_TEXTDOMAIN : define('THEMOSIS_TEXTDOMAIN', Themosis\Configuration\Application::get('textdomain'));

/*----------------------------------------------------
| Set framework main configuration
|
|---------------------------------------------------*/
Themosis\Configuration\Configuration::make();

/*----------------------------------------------------
| Application constants
|
|---------------------------------------------------*/
Themosis\Configuration\Constant::load();

/*----------------------------------------------------
| Add global helpers functions
|
|---------------------------------------------------*/
include_once(themosis_path('sys').'Helpers'.DS.'helpers.php');

/*----------------------------------------------------
| Themosis Page Templates.
|
|---------------------------------------------------*/
Themosis\Configuration\Template::init();

/*----------------------------------------------------
| Register image sizes.
|
|---------------------------------------------------*/
Themosis\Configuration\Images::install();

/*----------------------------------------------------
| Load the models.
|
|---------------------------------------------------*/
Themosis\Core\ModelLoader::add();
Themosis\Core\ModelLoader::alias();

/*----------------------------------------------------
| Parse application files and include them.
| Extends the 'functions.php' file by loading
| files located under the 'admin' folder.
|
|---------------------------------------------------*/
Themosis\Core\AdminLoader::add();
Themosis\Core\WidgetLoader::add();

/*----------------------------------------------------
| Load custom widgets
|
|---------------------------------------------------*/
Themosis\Core\WidgetLoader::load();

/*----------------------------------------------------
| Install global JS variables
|
|---------------------------------------------------*/
Themosis\Ajax\Ajax::set();

/*----------------------------------------------------
| Handle core frameworks assets
|
|---------------------------------------------------*/
// Themosis custom styles
Themosis\Facades\Asset::add('themosis_core_styles', 'css/_themosis-core.css')->to('admin');

// Themosis custom scripts
Themosis\Facades\Asset::add('themosis_core_scripts', 'js/_themosis-core.js', array('jquery', 'jquery-ui-sortable', 'underscore', 'backbone'), false, true)->to('admin');

/*----------------------------------------------------
| Handle all errors, warnings, exceptions
|
|---------------------------------------------------*/
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
	if (class_exists('Themosis\Error\Error')) {
		Themosis\Error\Error::native($code, $error, $file, $line);
	}
});

if (Themosis\Configuration\Error::get('shutdown')) {
	register_shutdown_function(function()
	{
		Themosis\Error\Error::shutdown();
	});
}

// Passing in the value -1 will show every
// possible error, even when new levels and
// constants are added in future PHP versions.
error_reporting(Themosis\Configuration\Error::get('report'));

// ---------------------------------------------------
// Done
// ---------------------------------------------------