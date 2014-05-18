<?php
namespace Themosis\Configuration;

use Themosis\Field\Field;
use Themosis\Metabox\Metabox;

defined('DS') or die('No direct script access.');

class Template extends ConfigTemplate
{
	/**
	 * Save the retrieved datas.
	*/
	protected static $datas = array();

	/**
	 * Init the page template module.
     *
     * @return void
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
		//$fields[] = Field::select('_themosisPageTemplate', $defaultNames, false, array('title' => __('Template', THEMOSIS_TEXTDOMAIN)));
		/*Metabox::make('Themosis Page Template', 'page', array('context' => 'side', 'priority' => 'core'))->set($fields);*/
	}

	/**
	 * Get the template names data and return them
	 *
	 * @return array An array of template names.
	 */
	private static function names()
	{
		$names = array();

		foreach (static::$datas as $name) {
			$names[$name] = ucfirst(trim($name));
		}

		return $names;

	}

}