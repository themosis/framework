<?php defined('DS') or die('No direct script access.');

/**
 * Init variables.
*/
$sections = array();
$settings = array();

/****************************************************************/
// MAINTENANCE MODE
/****************************************************************/
/**
 * Set the section for the maintenance option page
*/
$sections[] = Field::section('themosis-maintenance', array('title' => __('Maintenance Mode', THEMOSIS_TEXTDOMAIN)));

/**
 * Set the maintenance settings
 * 1 - Enable the maintenance
 * 2 - Define retry-after time in seconds
*/
/**
 * 1 - Enable
*/
$settings[] = Field::radio('activate', array('yes', 'no'), array('title' => __('Enable', THEMOSIS_TEXTDOMAIN), 'section' => 'themosis-maintenance', 'default' => array('no')));

/**
 * 2 - Time retry-after
*/
$settings[] = Field::text('duration', array('default' => 3600, 'title' => __('Retry Time', THEMOSIS_TEXTDOMAIN), 'section' => 'themosis-maintenance', 'class' => 'numeric', 'info' => __('Tell bots when to scan the website again. Enter a number in seconds.', THEMOSIS_TEXTDOMAIN)));

/**
 * Handle maintenance mode
*/
add_action('pre_get_posts', function($query){

	list($maintenance) = Option::get('themosis-maintenance', 'activate');

	if ($maintenance === 'yes'):

		// Check the user
		list($user) = (User::get()->roles) ? User::get()->roles : array('');
		
		if ($user !== 'administrator') {

			// Reset the main WP query var
			$query->init();

		}

	endif;

});

/****************************************************************/
// Themosis options page.
/****************************************************************/
$page = Page::make('Themosis', 'themosis-main-settings', $sections, $settings);
$page->setMenuIcon(plugins_url(THFWK_Themosis::getInstance()->getDir()).DS.'src'.DS.'Themosis'.DS.'_assets'.DS.'images'.DS.'themosisOptionsIcon_0001.png');
$page->set();