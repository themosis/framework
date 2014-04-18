<?php
namespace Themosis\Taxonomy;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class Taxonomy
{
	/**
	 * Keep a copy of its slug.
	*/
	private $slug;

	/**
	 * Keep a copy of its associated post type.
	*/
	private $postType;

	/**
	 * Keep a copy of its data object
	*/
	private $data;

    /**
     * The Taxonomy constructor.
     *
     * @param string $slug The slug name of the custom taxonomy.
     * @param string $name The display name of the custom taxonomy.
     * @param string $postType The slug of the post type to associate with.
     * @ignore
     */
	public function __construct($slug, $name, $postType)
	{
		$this->slug = $slug;
		$this->postType = $postType;

		$this->data = new TaxonomyData($name);

		Action::listen('init', $this, 'install')->dispatch();

	}

    /**
     * Build a new taxonomy.
     * Pass the taxonomy slug, its name and the post type
     * you want to attach it to.
     *
     * @param string $slug The slug name of the custom taxonomy.
     * @param string $name The display name of the custom taxonomy.
     * @param string $postType The slug of the post type to associate with.
     * @throws TaxonomyException
     * @return \Themosis\Taxonomy\Taxonomy
     */
	public static function make($slug, $name, $postType){

		if (is_string($slug) && is_string($name) && is_string($postType)) {

			$slug = trim($slug);
			$name = ucfirst(trim($name));

			return new static($slug, $name, $postType);

		} else {
			throw new TaxonomyException("Invalid taxonomy parameters.");
		}
	}

	/**
	 * Install the taxonomy in the WP administration.
	 * Executed by the Event class 'init' hook.
     *
     * @return void
     * @ignore
	 */
	public function install()
	{
		register_taxonomy($this->slug, $this->postType, $this->data->get());
	}

    /**
     * Override or add properties to the custom taxonomy
     * by passing an array.
     *
     * @param array $params The register_taxonomy arguments.
     * @throws TaxonomyException
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * @return void
     */
	public function set(array $params)
	{
		if (is_array($params)) {
			$this->data->set($params);
		} else {
			throw new TaxonomyException("Invalid parameter, only accepts array.");
		}
	}
}