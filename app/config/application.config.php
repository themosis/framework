<?php

return array(

	/*
	* Edit this file in order to configure your application
	* settings or preferences.
	* 
	*/

	/* --------------------------------------------------------------- */
	// Plugin textdomain
	/* --------------------------------------------------------------- */
	'textdomain'    => 'themosis',

	/* --------------------------------------------------------------- */
	// Global Javascript namespace of your application
	/* --------------------------------------------------------------- */
	'namespace'     => 'themosis',

	/* --------------------------------------------------------------- */
	// Set WordPress admin ajax file without the PHP extension
	/* --------------------------------------------------------------- */
	'ajaxurl'	    => 'admin-ajax',

	/* --------------------------------------------------------------- */
	// Encoding
	/* --------------------------------------------------------------- */
	'encoding'	    => 'UTF-8',
	
	/* --------------------------------------------------------------- */
	// Allow to define the path for the login page
	/* --------------------------------------------------------------- */
	'loginurl'	    => 'login',

	/* --------------------------------------------------------------- */
	// Cleanup Header
	/* --------------------------------------------------------------- */
	'cleanup'	    => true,

	/* --------------------------------------------------------------- */
	// Restrict access to the WordPress Admin for users with a
	// specific role. 
	// Once the theme is activated, you can only log in by going
	// to 'wp-login.php' or 'login' (if permalinks changed) urls.
	// By default, allows 'administrator', 'editor', 'author',
	// 'contributor' and 'subscriber' to access the ADMIN area.
	// Edit this configuration in order to limit access.
	/* --------------------------------------------------------------- */
	'access'	    => array(
		'administrator',
		'editor',
		'author',
		'contributor',
		'subscriber'
	),

	/* --------------------------------------------------------------- */
	// Application classes' alias
	/* --------------------------------------------------------------- */
	'aliases'	    => array(
		'Themosis\\Ajax\\Ajax'						=> 'Ajax',
		'Themosis\\Facades\\Asset'					=> 'Asset',
		'Themosis\\Configuration\\Application'		=> 'Application',
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
		'Themosis\\View\\Loop'						=> 'Loop',
		'Themosis\\Facades\\View'					=> 'View'
	)

);