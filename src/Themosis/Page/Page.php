<?php
namespace Themosis\Page;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class Page
{

	/**
	 * Page datas
	*/
	private $data;

	/**
	 * Page renderer object
	*/
	private $renderer;

	/**
	 * Build event
	*/
	private $buildEvent;

	/**
	 * Menu icon url
	*/
	private $iconUrl = '';

	/**
	 * Page capability
	*/
	private $cap = 'manage_options';

	public function __construct($params)
	{
		$this->data = new PageData($params);
		$this->renderer = new PageRenderer($this->data);

		$this->buildEvent = Action::listen('admin_menu', $this, 'build');
		Action::listen('admin_init', $this, 'install')->dispatch();
		Action::listen('admin_enqueue_scripts', $this, 'enqueueMediaUploader')->dispatch();
	}

	/**
	 * Define an options page. Can be a main or submenu page.
	 * 
	 * @param string
	 * @param string
	 * @param array
	 * @param array
	 * @param string (optional)
	 * @return object
	*/
	public static function make($title, $slug, $sections, $settings, $parent = null)
	{
		if (is_string(trim($title)) && is_string(trim($slug)) && is_array($sections) && is_array($settings)) {

			$params = compact('title', 'slug', 'sections', 'settings', 'parent');

			return new static($params);

		} else {
			throw new PageException("Invalid page parameters.");
		}
	}

	/**
	 * Build the options page.
	 * 
	 * @return object
	*/
	public function set()
	{
		$this->buildEvent->dispatch();

		return $this;
	}

	/**
	 * Define the menu icon url. Define the absolute URL.
	 * 
	 * @param string
	 * @return object
	*/
	public function setMenuIcon($url)
	{
		$this->iconUrl = (is_string($url) && !empty($url)) ? $url : '';

		return $this;
	}

	/**
	 * Define page capability.
	 * 
	 * @param string
	 * @param object
	*/
	public function setCap($cap)
	{
		$this->cap = (is_string($cap) && !empty($cap)) ? $cap : 'manage_options';

		return $this;
	}

	/**
	 * Construct the page
	*/
	public function build()
	{
		if (!is_null($this->data->get('parent'))) {
			add_submenu_page($this->data->get('parent'), $this->data->get('title'), $this->data->get('title'), $this->cap, $this->data->get('slug'), array(&$this, 'display'));
		} else {
			add_menu_page($this->data->get('title'), $this->data->get('title'), $this->cap, $this->data->get('slug'), array(&$this, 'display'), $this->iconUrl, null);
		}
	}

	/**
	 * Display the page
	*/
	public function display()
	{
		$this->renderer->page($this->data);
	}

	/**
	 * Install page settings
	*/
	public function install()
	{
		// If the theme options don't exist, create them.
		foreach ($this->data->get('sections') as $section) {
			if (get_option($section['name']) === false) {    
		    	add_option($section['name']);  
		    }	
		}

		// Display sections
		foreach($this->data->get('sections') as $section){
			add_settings_section($section['name'], $section['title'], array(&$this, 'displaySections'), $section['name']);
		}

		// Display settings
		foreach($this->data->get('settings') as $setting){
			add_settings_field($setting['name'], $setting['title'], array(&$this, 'displaySettings'), $setting['section'], $setting['section'], $setting);
		}

		// Register the settings and define the sanitized callback
		// Group all page settings in one, avoid polluting
		// the wp_options table.
		// When you want to retrieve a setting use the option_group
		// name and the setting id. Check documentation for the
		// Option class of the Themosis utility framework.
		foreach ($this->data->get('sections') as $section) {
			register_setting($section['name'], $section['name'], array(&$this, 'validate'));	
		}
	}

	/**
	 * Handle section display
	*/
	public function displaySections($args)
	{

	}

	/**
	 * Handle settings display
	 * 
	 * @param array
	*/
	public function displaySettings($args)
	{
		$this->renderer->settings($args);
	}

	/**
	 * Validate settings
	 * 
	 * @param array
	*/
	public function validate($input)
	{
		return $this->data->validate($input);
	}

	/**
	 * Enqueue the new WP > 3.5 media Uploader
	*/
	public function enqueueMediaUploader()
	{
		// If WP > 3.5
		if (get_bloginfo('version') >= 3.5) {
			wp_enqueue_media();
		}
	}

}