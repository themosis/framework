<?php
namespace Themosis\Configuration;

use Themosis\Facades\Field;
use Themosis\Facades\Metabox;

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
		$templateNames = array_merge(array('none' => __('- None -')), static::names());

		// Build a select field
		Metabox::make('Themosis Page Template', 'page', array('context' => 'side', 'priority' => 'core'))->set(array(
            Field::select('_themosisPageTemplate', array($templateNames), false, array('title' => __('Template', THEMOSIS_FRAMEWORK_TEXTDOMAIN)))
        ));
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
			$names[$name] = str_replace(array('-', '_'), ' ', ucfirst(trim($name)));
		}

		return $names;

	}

}