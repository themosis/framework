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

    /**
     * The Page constructor.
     *
     * @param array $params The page datas.
     */
	public function __construct(array $params)
	{
		$this->data = new PageData($params);
		$this->renderer = new PageRenderer($this->data);

		$this->buildEvent = Action::listen('admin_menu', $this, 'build');
		Action::listen('admin_init', $this, 'install')->dispatch();
		Action::listen('admin_enqueue_scripts', $this, 'enqueueMediaUploader')->dispatch();
	}

    /**
     * Define an options page. Can be a main or sub-menu page.
     *
     * @todo Move the $sections and $settings to the 'set()' method.
     *
     * @param string $title The page display title.
     * @param string $slug The page slug name.
     * @param array $sections A list of sections.
     * @param array $settings A list of settings, fields.
     * @param string $parent The slug of the parent page.
     * @throws PageException
     * @return object A Themosis\Page\Page instance.
     */
	public static function make($title, $slug, array $sections, array $settings, $parent = null)
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
	 * @return \Themosis\Page\Page
	 */
	public function set()
	{
		$this->buildEvent->dispatch();

		return $this;
	}

	/**
	 * Define the menu icon url.
	 * 
	 * @param string $url The absolute URL to the icon.
	 * @return \Themosis\Page\Page
	 */
	public function setMenuIcon($url)
	{
		$this->iconUrl = (is_string($url) && !empty($url)) ? $url : '';

		return $this;
	}

    /**
     * Define page capability.
     *
     * @param string $cap The capability name.
     * @return \Themosis\Page\Page
     */
	public function setCap($cap)
	{
		$this->cap = (is_string($cap) && !empty($cap)) ? $cap : 'manage_options';

		return $this;
	}

	/**
	 * Construct the page.
     *
     * @return void
	 */
	public function build()
	{
		if (!is_null($this->data->get('parent'))) {
			add_submenu_page($this->data->get('parent'), $this->data->get('title'), $this->data->get('title'), $this->cap, $this->data->get('slug'), array($this, 'display'));
		} else {
			add_menu_page($this->data->get('title'), $this->data->get('title'), $this->cap, $this->data->get('slug'), array($this, 'display'), $this->iconUrl, null);
		}
	}

	/**
	 * Display the page.
     *
     * @return void
	 */
	public function display()
	{
		$this->renderer->page($this->data);
	}

	/**
	 * Install page settings.
     *
     * @return void
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
			add_settings_section($section['name'], $section['title'], array($this, 'displaySections'), $section['name']);
		}

		// Display settings
		foreach($this->data->get('settings') as $setting){
			add_settings_field($setting['name'], $setting['title'], array($this, 'displaySettings'), $setting['section'], $setting['section'], $setting);
		}

		// Register the settings and define the sanitized callback
		// Group all page settings in one, avoid polluting
		// the wp_options table.
		// When you want to retrieve a setting use the option_group
		// name and the setting id. Check documentation for the
		// Option class of the Themosis utility framework.
		foreach ($this->data->get('sections') as $section) {
			register_setting($section['name'], $section['name'], array($this, 'validate'));
		}
	}

    /**
     * Handle section display.
     *
     * @param array $args The section properties.
     * @return void
     */
	public function displaySections(array $args)
	{
        // Customize the section display.
	}

	/**
	 * Handle settings display.
	 * 
	 * @param array $args The setting properties.
     * @return void
	 */
	public function displaySettings(array $args)
	{
		$this->renderer->settings($args);
	}

    /**
     * Validate settings.
     *
     * @param array $input The option field values.
     * @return array The sanitized field values.
     */
	public function validate(array $input)
	{
		return $this->data->validate($input);
	}

	/**
	 * Enqueue the new WP > 3.5 media Uploader.
     *
     * @return void
	 */
	public function enqueueMediaUploader()
	{
		// If WP > 3.5
		if (get_bloginfo('version') >= 3.5) {
			wp_enqueue_media();
		}
	}

}