<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;
use Themosis\Field\Field;
use Themosis\Metabox\Meta;
use Themosis\Metabox\Metabox;
use Themosis\Route\Route;
use Themosis\View\View;

defined('DS') or die('No direct script access.');

class Template extends ConfigTemplate
{
	/**
	 * Save the retrieved datas
	*/
	protected static $datas = array();

	public function __construct()
	{
		//Action::listen('themosis_parse_query', $this, 'parseQuery')->dispatch();
	}

	/**
	 * Init the page template module
	*/
	public static function init()
	{
		if (empty(static::$datas)) {
			return;
		}

		// Set an empty value for no templates.
		$defaultNames = array();

		$templateNames = array_merge(array('none' => __('No template')), static::names());

		$defaultNames[] = $templateNames;

		// Build a select field
		$fields[] = Field::select('_themosisPageTemplate', $defaultNames, false, array('title' => __('Template', THEMOSIS_TEXTDOMAIN)));
		Metabox::make('Themosis Page Template', 'page', array('context' => 'side', 'priority' => 'core'))->set($fields);
	}

	/**
	 * Get the template names data and return them
	 *
	 * @return array
	*/
	private static function names()
	{
		$names = array();

		foreach (static::$datas as $name) {
			$names[$name] = ucfirst(trim($name));
		}

		return $names;

	}

	/**
	 * Parse the queries before routes.php
	*/
	public function parseQuery()
	{
		global $wp_query;

		if (isset($wp_query->queried_object)) {

			$queriedObject = $wp_query->queried_object;

			if (is_a($queriedObject, 'WP_Post') && 'page' === $queriedObject->post_type) {

				// Check if there is a value for a template
				$this->checkTemplate($queriedObject);

			}

		}

	}

	/**
	 * Check for a template
	 *
	 * @param object
	*/
	private function checkTemplate($post)
	{
		// Sanitized value
		$template = Meta::get($post->ID, '_themosisPageTemplate');

		// If no template selected, just return;
		if ($template === 'none') return;

		// Send the appropriate view
		if (isset($template) && !empty($template)) {

			foreach (static::$datas as $name => $path) {

				if ($name === $template) {

					// The template exists and should be used.
					// Inform that the template is used in place of
					// the routes.php file
					Route::template('page', $post->post_name, function() use ($path){

						return View::make($path);

					});

				}

			}

		}

	}

}