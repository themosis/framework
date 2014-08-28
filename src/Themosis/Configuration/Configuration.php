<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;

class Configuration
{
	/**
	 * Configuration instance is unique.
	*/
	private static $instance = null;

	/**
     * The Configuration constructor.
	 */
	private function __construct()
	{
		Action::listen('init', $this, 'init')->dispatch();
	}

    /**
     * Start the configuration.
     *
     * @return \Themosis\Configuration\Configuration
     */
	public static function make()
	{
		if (is_null(static::$instance))
        {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Run a series of methods at WP init hook
     *
     * @return void
	 */
	public function init()
	{
		if (Application::get('cleanup')) $this->cleanup();

		$access = Application::get('access');
		if (!empty($access) && is_array($access)) $this->restrict();
	}

	/**
	 * Cleanup the WP head tag.
     *
     * @return void
	 */
	private function cleanup()
	{
		global $wp_widget_factory;
			
		remove_action('wp_head', 'feed_links', 2);
		remove_action('wp_head', 'feed_links_extra', 3);
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

        if (array_key_exists('WP_Widget_Recent_Comments', $wp_widget_factory->widgets))
        {
            remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
        }

		add_filter('use_default_gallery_style', '__return_null');
	}

	/**
	 * Restrict access to the wp-admin.
     *
     * @return void
	 */
	private function restrict()
	{
		$access = Application::get('access');

	    if (is_admin())
        {
            $user = wp_get_current_user();
	    	$role = $user->roles;
	    	$role = (count($role) > 0) ? $role[0] : '';

	    	if (!in_array($role, $access) && !(defined('DOING_AJAX') && DOING_AJAX)  && !(defined('WP_CLI') && WP_CLI))
            {
	    		wp_redirect(home_url());
	    		exit;
	    	}
	    }
		
	}
}