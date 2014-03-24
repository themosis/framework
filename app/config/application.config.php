<?php defined('DS') or die('No direct script access.');

return array(

	/*
	* Edit this file in order to configure your application
	* settings or preferences.
	* 
	*/

	/* --------------------------------------------------------------- */
	// Theme textdomain
	/* --------------------------------------------------------------- */
	'textdomain'	=> 'themosis',

	/* --------------------------------------------------------------- */
	// Global Javascript namespace of your application
	/* --------------------------------------------------------------- */
	'namespace'	=> 'themosis',

	/* --------------------------------------------------------------- */
	// Set wordpress admin ajax file without the PHP extension
	/* --------------------------------------------------------------- */
	'ajaxurl'	=> 'admin-ajax',

	/* --------------------------------------------------------------- */
	// Encoding
	/* --------------------------------------------------------------- */
	'encoding'	=> 'UTF-8',

	/* --------------------------------------------------------------- */
	// Rewrite - If you want to modify default Wordpress paths.
	// If you change this parameter, you need to go to the 'Permalinks'
	// tab in the Wordpress admin and update the structure by saving
	// the changes.
	/* --------------------------------------------------------------- */
	'rewrite'	=> false,
	
	/* --------------------------------------------------------------- */
	// Allow to define the path for the login page
	/* --------------------------------------------------------------- */
	'loginurl'	=> 'login',

	/* --------------------------------------------------------------- */
	// Cleanup Header
	/* --------------------------------------------------------------- */
	'cleanup'	=> true,

	/* --------------------------------------------------------------- */
	// Add custom htaccess settings.
	// The settings are a mix of Themosis parameters and HTML5 Boilerplate
	// htaccess settings.
	// Will overwrite your htacess settings each time
	// you go to the permalinks settings page in the admin.
	// If you edit your main .htaccess file and you want to avoid the
	// framework to overwrite your settings, set this to "false".
	/* --------------------------------------------------------------- */
	'htaccess'	=> true,

	/* --------------------------------------------------------------- */
	// Restrict access to the Wordpress Admin for users with a
	// specific role. 
	// Once the theme is activated, you can only log in by going
	// to 'wp-login.php' or 'login' (if permalinks changed) urls.
	// By default, allows 'administrator', 'editor', 'author',
	// 'contributor' and 'subscriber' to access the ADMIN area.
	// Edit this configuration in order to limit access.
	/* --------------------------------------------------------------- */
	'access'	=> array(
		'administrator',
		'editor',
		'author',
		'contributor',
		'subscriber'
	),

	/* --------------------------------------------------------------- */
	// Application classes's alias
	/* --------------------------------------------------------------- */
	'aliases'	=> array(
		'Themosis\\Ajax\\Ajax'						=> 'Ajax',
		'Themosis\\Asset\\Asset'					=> 'Asset',
		'Themosis\\Configuration\\Application'		=> 'Application',
		'Themosis\\Model\\BaseModel'                => 'BaseModel',
		'Themosis\\Field\\Field'					=> 'Field',
		'Themosis\\Html\\Form'						=> 'Form',
		'Themosis\\Metabox\\Meta'					=> 'Meta',
		'Themosis\\Metabox\\Metabox'				=> 'Metabox',
		'Themosis\\Page\\Option'					=> 'Option',
		'Themosis\\Page\\Page'						=> 'Page',
		'Themosis\\PostType\\PostType'				=> 'PostType',
		'Themosis\\Route\\Route'					=> 'Route',
		'Themosis\\Session\\Session'				=> 'Session',
		'Themosis\\Taxonomy\\TaxField'              => 'TaxField',
		'Themosis\\Taxonomy\\TaxMeta'               => 'TaxMeta',
		'Themosis\\Taxonomy\\Taxonomy'				=> 'Taxonomy',
		'Themosis\\User\\User'						=> 'User',
		'Themosis\\View\\Loop'						=> 'Loop',
		'Themosis\\View\\View'						=> 'View'
	)

);

?>