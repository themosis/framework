<?php
namespace Themosis\PostType;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class PostType {

	/**
	 * Keep a reference to its data object
	*/
	private $data;

	/**
	 * Keep a reference to its slug
	*/
	private $slug;

	/**
	 * Event object
	*/
	private $event;

	/**
	 * Media event
	*/
	private $mediaEvent;

	/**
	 * Use restful
	*/
	private $restful = false;

	public function __construct($slug, $name)
	{
		$this->slug = $slug;
		$this->data = new PostTypeData($name);

		$this->event = Action::listen('init', $this, 'register');
		$this->mediaEvent = Action::listen('admin_enqueue_scripts', $this, 'enqueueMediaUploader');
	}

    /**
     * Used to build a new custom post type.
     * It will use default datas from the PostTypeData class.
     *
     * @param string $slug The post type slug name.
     * @param string $name The plural display name.
     * @throws PostTypeException
     * @return object A Themosis\PostType\PostType instance.
     */
	public static function make($slug, $name)
	{
		if (is_string($slug) && is_string($name)) {

			$name = ucfirst(trim($name));

			return new self($slug, $name);
		} else {
			throw new PostTypeException('Invalid custom post type parameters. Make sure you\'re using 2 string parameters.');
		}
	}

	/**
	 * Override or add properties to the custom post type
	 * by passing an array.
	 * Also install the custom post type.
	 *
	 * @param array $params The custom post type arguments: https://codex.wordpress.org/Function_Reference/register_post_type
	 * @return object A Themosis\PostType\PostType instance.
	 */
	public function set(array $params = array())
	{
		if (is_array($params) && !empty($params)) {

			$this->data->set($params);

			// If no editor
			// Load the new WP media uploader
			if (!$this->data->handleEditor()) {
				$this->mediaEvent->dispatch();
			}

		}

		// Trigger the register method
		$this->event->dispatch();

		return $this;
	}

	/**
	 * Retrieve the custom post type slug
	 *
	 * @return string The custom post type slug property.
	*/
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * Retrieve the custom post type datas
	 *
	 * @return object A Themosis\PostType\PostTypeData instance.
	*/
	public function getData()
	{
		return $this->data->get();
	}

	/**
	 * Register the custom post type
	 *
	 * @return object|WP_Error The custom post type object or a WP_Error instance if error.
	*/
	public function register()
	{
		if ($this->restful) {
			// Rewrite rules for the custom post types
			$this->rewrite();
		}

		return register_post_type($this->slug, $this->data->getArgs());
	}

	/**
	 * Enqueue the new media uploader
	 *
	 * @return bool True. False if not enqueue.
	*/
	public function enqueueMediaUploader()
	{
		// If WP > 3.5
		if (get_bloginfo('version') >= 3.5) {
			wp_enqueue_media();

			return true;
		}

		return false;
	}

	/**
	 * Tell the Custom Post Type to be RESTful.
     *
     * @todo Remove restful methods. Wait for core restul API.
	 *
     * @deprecated No longer used by internal code and not recommended.
	 * @return object A Themosis\PostType\PostType instance.
	*/
	public function isRestful()
	{
		$this->data->rest();
		$this->restful = true;

		return $this;
	}

	/**
	 * Handle custom rewrite for the custom post type.
     *
     * @return void
	*/
	private function rewrite()
	{
		// Overwrite the custom post type query var
		$this->rewriteTag();

		// Add RESTful rules
		$this->rewriteRules();
	}

	/**
	 * Add the query var. Is similar to the post type
	 * slug.
     *
     * @return void
	*/
	private function rewriteTag()
	{
		add_rewrite_tag('%'.$this->slug.'%','([^&]+)', 'books');
	}

	/**
	 * Add rewrite rules in order to handle RESTful.
     *
     * @deprecated No longer used by internal code and not recommended.
     * @return void
	*/
	private function rewriteRules()
	{
		// Catch ALL custom post types
		add_rewrite_rule('^'.$this->slug.'/','index.php?post_type='.$this->slug,'top');

		add_rewrite_rule('^'.$this->slug.'/(0-9)/?', 'index.php?post_type='.$this->slug.'&p=$matches[1]','top');
	}

}

?>