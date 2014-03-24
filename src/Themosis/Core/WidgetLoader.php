<?php
namespace Themosis\Core;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class WidgetLoader extends Loader implements LoaderInterface
{
	/**
	 * Widget directory path
	*/
	private static $path;

	/**
	 * Exclude widgets names
	*/
	private static $excludedWidgets = array('index', 'Index', 'widget', 'Widget');

	/**
	 * Widgets
	*/
	private static $widgets = array();

	/**
	 * List of filenames
	*/
	protected static $names = array();

	public function __construct()
	{
		Action::listen('widgets_init', $this, 'install')->dispatch();
	}

	/**
	 * Build the path where the class has to scan
	 * the files for adding the WIDGETS.
	 * 
	 * @return boolean
	*/
	public static function add()
	{
		static::$path = themosis_path('datas').'widgets'.DS;
		return static::append(static::$path);
	}

	/**
	 * Load custom widgets
	 * 
	 * @return object
	*/
	public static function load()
	{
		foreach (static::$names as $name) {
			if (!in_array($name, static::$excludedWidgets)) {
				$name = $name.'_Widget';
				static::$widgets[] = $name;
			}
		}

		return new static();

	}

	/**
	 * Install the widgets
	 * 
	 * @return boolean
	*/
	public static function install()
	{
		if (count(static::$widgets) > 0) {
			foreach (static::$widgets as $widget) {
				register_widget($widget);
			}
			return true;
		}
		return false;
	}

}