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
		Action::listen('generate_rewrite_rules', $this, 'rewrite')->dispatch();

		if (Application::get('rewrite') && !is_admin()) {
			add_filter('script_loader_src', array($this, 'rewriteAssetUrl'));
			add_filter('style_loader_src', array($this, 'rewriteAssetUrl'));
			add_filter('stylesheet_directory_uri', array($this, 'rewriteAssetUrl'));
			add_filter('template_directory_uri', array($this, 'rewriteAssetUrl'));
			add_filter('bloginfo', array($this, 'rewriteAssetUrl'));
			add_filter('plugins_url', array($this, 'rewriteAssetUrl'));
		}

		// Admin actions
		Action::listen('admin_head', $this, 'adminHead')->dispatch();
	}

    /**
     * Start the configuration.
     *
     * @return \Themosis\Configuration\Configuration
     */
	public static function make()
	{
		if (is_null(static::$instance)) {
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
	 * Run a series of methods when changing WP permalinks.
	 * Run at 'generate_rewrite_rules' action.
	 * 
	 * @param \WP_Rewrite $rewriteObject The WordPress WP_Rewrite instance.
     * @return void
	 */
	public function rewrite(\WP_Rewrite $rewriteObject)
	{
		// Add HTML5 BoilerPlate htaccess default settings.
		if (Application::get('htaccess')) $this->addHtaccess($rewriteObject);

		// Rewrite assets URLs
		if (Application::get('rewrite')) $this->rewritePaths($rewriteObject);
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
		remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
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

	    if (is_admin()) {

            $user = wp_get_current_user();
	    	$role = $user->roles;
	    	$role = (count($role) > 0) ? $role[0] : '';

	    	if (!in_array($role, $access) && !(defined('DOING_AJAX') && DOING_AJAX)  && !(defined('WP_CLI') && WP_CLI)) {
	    		wp_redirect(home_url());
	    		exit;
	    	}

	    }
		
	}

    /**
     * When editing the permalink structure in the admin,
     * we check if the .htaccess file as the HTMLBP htaccess settings in.
     * If not, add them.
     *
     * @param \WP_Rewrite $rewriteObject The WordPress WP_Rewrite instance.
     * @return \WP_Rewrite
     */
	private function addHtaccess(\WP_Rewrite $rewriteObject)
	{
		$homePath = function_exists('get_home_path') ? get_home_path() : ABSPATH;
		$htaccessFile = $homePath . '.htaccess';
		$modRewriteEnabled = function_exists('got_mod_rewrite') ? got_mod_rewrite() : false;

		if ((!file_exists($htaccessFile) && is_writable($homePath) && $rewriteObject->using_mod_rewrite_permalinks()) || is_writable($htaccessFile)) {

			if ($modRewriteEnabled) {

				// By default there is no 'THEMOSIS' datas
				$themosisRules = extract_from_markers($htaccessFile, 'THEMOSIS');

				if ($themosisRules === array()) {

					$filename = themosis_path('sys').DS.'Configuration'.DS.'Utilities'.DS.'ThemosisHtaccess';

					return insert_with_markers($htaccessFile, 'THEMOSIS', extract_from_markers($filename, 'THEMOSIS'));

				}
			}
		}

		return $rewriteObject;
	}

	/**
	 * Rewrite assets URLs. It only adds them to the
	 * htaccess file. It does not modify them at output!
	 * See filters to modify the assets paths.
	 * 
	 * Core rewrite works but the admin bar css doesn't display correctly
	 * in the front-end. Relatives paths to the admin sprite image file don't work.
	 * 
	 * @param \WP_Rewrite $rewriteObject The WordPress WP_Rewrite instance.
     * @return void
	 */
	private function rewritePaths(\WP_Rewrite $rewriteObject)
	{
		$themeName = $this->getThemeName();

		$nonWpRules = array(
			// CORE ASSETS
			'libraries/js/(.*)'			=> 'wp-includes/js/$1',
			'libraries/css/(.*)'		=> 'wp-includes/css/$1',
			'libraries/images/(.*)'		=> 'wp-includes/images/$1',

			// LOGIN
			Application::get('loginurl').'/?$'		=> 'wp-login.php',

			// AJAX URL
			'ajax/'.Application::get('ajaxurl').'.php' 	=> $this->getAdminPath().Application::get('ajaxurl').'.php',

			// PLUGINS
			'plugins/(.*)'		=> 'wp-content/plugins/$1',

			// THEME ASSETS
			'assets/css/(.*)'		=> 'wp-content/themes/'.$themeName.'/app/assets/css/$1',
			'assets/js/(.*)'		=> 'wp-content/themes/'.$themeName.'/app/assets/js/$1',
			'assets/images/(.*)'	=> 'wp-content/themes/'.$themeName.'/app/assets/images/$1'
		);

		$rewriteObject->non_wp_rules += $nonWpRules;
	}

	/**
	 * Rewrite assets urls.
	 * Core assets, theme assets and further plugins.
	 * 
	 * @param string $url The url to rewrite.
	 * @return string The converted url.
	 */
	public function rewriteAssetUrl($url)
	{
		// Change the THEME assets urls
		if (strpos($url, $this->getThemePath()) !== false) {
			
			return str_replace('/'.$this->getThemePath().'/app', '', $url);
		
		}

		// Change the WP CORE assets urls
		if (strpos($url, $this->getCorePath()) !== false) {
			
			return str_replace($this->getCorePath(), 'libraries/', $url);
		
		}

		// Change PLUGINS urls
		if (strpos($url, $this->getPluginsPath()) !== false) {

			$paths = explode($this->getPluginsPath(), $url);

			return $paths[0].'plugins'.$paths[1];

		}

		return $url;
	}

	/**
	 * Allow developers to add parameters to the admin global JS object.
	 * 
	 * @return void
	 */
	public function adminHead()
	{
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
	}

	/**
	 * Return the theme folder name.
	 * 
	 * @return string The theme folder name.
	 */
	private function getThemeName()
	{
		$name = explode('/themes/', get_stylesheet_directory());

		return next($name);
	}

	/**
	 * Retrieve the relative WP-CONTENT path.
	 * 
	 * @return string The 'wp-content' relative path.
	 */
	private function getWpContentPath()
	{
		return str_replace(site_url().'/', '', content_url());
	}

	/**
	 * Retrieve the relative PLUGINS path.
     *
     * @return string The relative 'plugins' path.
	 */
	private function getPluginsPath()
	{
		return str_replace(site_url().'/', '', content_url().'/plugins');
	}

	/**
	 * Return the relative Theme path
	 * 
	 * @return string The relative 'themes' path.
	 */
	private function getThemePath()
	{
		return $this->getWpContentPath().'/themes/'.$this->getThemeName();
	}

	/**
	 * Return the wp-include relative path.
	 * 
	 * @return string The relative 'wp-includes' path.
	 */
	private function getCorePath()
	{
		return str_replace(site_url().'/', '', includes_url());
	}

	/**
	 * Return the wp-admin relative path.
	 * 
	 * @return string The relative 'wp-admin' path.
	 */
	private function getAdminPath()
	{
		return str_replace(site_url().'/', '', admin_url());
	}
}