<?php defined('DS') or die('No direct script access.');

/**
 * Handle Core framework components
 * Used to bootstrap Themosis.
*/
/*----------------------------------------------------
| Themosis core constants
|
|
|
|
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
|
|
|
|
|---------------------------------------------------*/
$configs = array(
    'datas' => array(
        'application',
        'constants',
        'errors'
    )
);

Themosis\Configuration\Config::make($configs);
Themosis\Configuration\Config::set();

/*----------------------------------------------------
| Trigger for configurations
|
|
|
|
|
|---------------------------------------------------*/
do_action('themosis_configurations');

/*----------------------------------------------------
| Set application classes's alias
|
|
|
|
|
|---------------------------------------------------*/
foreach (Themosis\Configuration\Application::get('aliases') as $namespace => $className){
	class_alias($namespace, $className);
}

/*----------------------------------------------------
| Themosis textdomain
|
|
|
|
|
|---------------------------------------------------*/
defined('THEMOSIS_TEXTDOMAIN') ? THEMOSIS_TEXTDOMAIN : define('THEMOSIS_TEXTDOMAIN', Themosis\Configuration\Application::get('textdomain'));

/*----------------------------------------------------
| Set framework main configuration
|
|
|
|
|
|---------------------------------------------------*/
Themosis\Configuration\Configuration::make();

/*----------------------------------------------------
| Application constants
|
|
|
|
|
|---------------------------------------------------*/
Themosis\Configuration\Constant::load();

/*----------------------------------------------------
| Add global helpers functions
|
|
|
|
|
|---------------------------------------------------*/
include_once(themosis_path('sys').'Helpers/helpers.php');

/*----------------------------------------------------
| Set the request object - Helper class for manipulating
| PHP globals.
|
|
|
|
|---------------------------------------------------*/
Themosis\Route\Request::$foundation = Symfony\Component\HttpFoundation\Request::createFromGlobals();

/*----------------------------------------------------
| Themosis Page Templates
|
|
|
|
|
|---------------------------------------------------*/
Themosis\Configuration\Template::init();

/*----------------------------------------------------
| Parse application files and include them
| Extends the 'functions.php' file
| (available only for the Themosis - Datas plugin
| under the 'admin' folder).
|
|
|
|
|---------------------------------------------------*/
Themosis\Core\AdminLoader::add();
Themosis\Core\WidgetLoader::add();

/*----------------------------------------------------
| Load the models.
|
|
|
|
|
|
|---------------------------------------------------*/
Themosis\Core\ModelLoader::add();
Themosis\Core\ModelLoader::alias();

/*----------------------------------------------------
| Load custom widgets
|
|
|
|
|
|
|---------------------------------------------------*/
Themosis\Core\WidgetLoader::load();

/*----------------------------------------------------
| Install global JS variables
|
|
|
|
|
|
|---------------------------------------------------*/
Themosis\Ajax\Ajax::set();

/*----------------------------------------------------
| Handle core frameworks assets
|
|
|
|
|
|
|---------------------------------------------------*/
// Themosis custom styles
Themosis\Asset\AdminAsset::add('themosis_core_styles', 'css/themosis.css')->to('admin');

// Themosis custom scripts
Themosis\Asset\AdminAsset::add('themosis_core_metabox', 'js/metabox.js', array('jquery', 'jquery-ui-sortable', 'underscore', 'backbone'), false, true)->to('admin');

/*----------------------------------------------------
| Handle all errors, warnings, exceptions
|
|
|
|
|
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
// Done ! Handle all routes.
// ---------------------------------------------------
Themosis\Route\Router::init();