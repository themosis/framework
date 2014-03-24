<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;
use Themosis\User\User;

defined('DS') or die('No direct script access.');

class Configuration
{
	/**
	 * Configuration instance is unique.
	*/
	private static $instance = null;

	// Avoid building an instance the 'normal' way
	// by setting visibility to 'private'
	private function __construct()
	{
		Action::listen('init', $this, 'init')->dispatch();
		Action::listen('generate_rewrite_rules', $this, 'rewrite')->dispatch();

		if (Application::get('rewrite') && !is_admin()) {
			add_filter('script_loader_src', array(&$this, 'rewriteAssetUrl'));
			add_filter('style_loader_src', array(&$this, 'rewriteAssetUrl'));
			add_filter('stylesheet_directory_uri', array(&$this, 'rewriteAssetUrl'));
			add_filter('template_directory_uri', array(&$this, 'rewriteAssetUrl'));
			add_filter('bloginfo', array(&$this, 'rewriteAssetUrl'));
			add_filter('plugins_url', array(&$this, 'rewriteAssetUrl'));	
		}

		// Admin actions
		Action::listen('admin_head', $this, 'adminHead')->dispatch();
	}

	public static function make()
	{
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Run a series of methods at WP init hook
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
	 * @param object
	*/
	public function rewrite($rewriteObject)
	{
		// Add HTML5 BoilerPlate htaccess default settings.
		if (Application::get('htaccess')) $this->addHtaccess($rewriteObject);

		// Rewrite assets URLs
		if (Application::get('rewrite')) $this->rewritePaths($rewriteObject);
	}

	/**
	 * Cleanup the WP head tag.
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
	*/
	private function restrict()
	{
		$access = Application::get('access');

	    if (is_admin()) {
	    	
	    	$role = User::get()->roles;
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
	 * @param object WP_Rewrite
	 * @return object
	*/
	private function addHtaccess($rewriteObject)
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
	 * @param object
	*/
	private function rewritePaths($rewriteObject)
	{
		$themeName = $this->getThemeName();

		$nonWpRules = array(
			// CORE ASSETS
			'libraries'.DS.'js'.DS.'(.*)'			=> 'wp-includes'.DS.'js'.DS.'$1',
			'libraries'.DS.'css'.DS.'(.*)'			=> 'wp-includes'.DS.'css'.DS.'$1',
			'libraries'.DS.'images'.DS.'(.*)'		=> 'wp-includes'.DS.'images'.DS.'$1',

			// LOGIN
			Application::get('loginurl').DS.'?$'		=> 'wp-login'.EXT,

			// AJAX URL
			'ajax'.DS.Application::get('ajaxurl').EXT 	=> $this->getAdminPath().Application::get('ajaxurl').EXT,

			// PLUGINS
			'plugins'.DS.'(.*)'		=> 'wp-content'.DS.'plugins'.DS.'$1',

			// THEME ASSETS
			'assets'.DS.'css'.DS.'(.*)'		=> 'wp-content'.DS.'themes'.DS.$themeName.DS.'app'.DS.'assets'.DS.'css'.DS.'$1',  
			'assets'.DS.'js'.DS.'(.*)'		=> 'wp-content'.DS.'themes'.DS.$themeName.DS.'app'.DS.'assets'.DS.'js'.DS.'$1',  
			'assets'.DS.'images'.DS.'(.*)'	=> 'wp-content'.DS.'themes'.DS.$themeName.DS.'app'.DS.'assets'.DS.'images'.DS.'$1'
		);

		$rewriteObject->non_wp_rules += $nonWpRules;
	}

	/**
	 * Rewrite assets urls.
	 * Core assets, theme assets and further plugins.
	 * 
	 * @param string
	 * @return string
	*/
	public function rewriteAssetUrl($url)
	{
		// Change the THEME assets urls
		if (strpos($url, $this->getThemePath()) !== false) {
			
			return str_replace(DS.$this->getThemePath().DS.'app', '', $url);
		
		}

		// Change the WP CORE assets urls
		if (strpos($url, $this->getCorePath()) !== false) {
			
			return str_replace($this->getCorePath(), 'libraries'.DS, $url);
		
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

		?>
		<script type='text/javascript'>
  
  			//<![CDATA[
			var thfmk_themosis = {
				<?php
					if (!empty($datas)) {
						foreach ($datas as $key => $value) {
							echo "'$key'".": ".json_encode($value).",";
						}
					}
				?>
			};
			//]]>

		</script>
		<?php
	}

	/**
	 * Return the theme folder name
	 * 
	 * @return string
	*/
	private function getThemeName()
	{
		$name = explode(DS.'themes'.DS, get_stylesheet_directory());

		return next($name);
	}

	/**
	 * Retrieve the relative WP-CONTENT path
	 * 
	 * @return string
	*/
	private function getWpContentPath()
	{
		return str_replace(site_url().DS, '', content_url());
	}

	/**
	 * Retrieve the relative PLUGINS path.
	*/
	private function getPluginsPath()
	{
		return str_replace(site_url().DS, '', content_url().DS.'plugins');
	}

	/**
	 * Return the relative Theme path
	 * 
	 * @return string
	*/
	private function getThemePath()
	{
		return $this->getWpContentPath().DS.'themes'.DS.$this->getThemeName();
	}

	/**
	 * Return the wp-include relative path.
	 * 
	 * @return string
	*/
	private function getCorePath()
	{
		return str_replace(site_url().DS, '', includes_url());
	}

	/**
	 * Return the wp-admin relative path.
	 * 
	 * @return string
	*/
	private function getAdminPath()
	{
		return str_replace(site_url().DS, '', admin_url());
	}
}