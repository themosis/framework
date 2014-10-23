<?php
namespace Themosis\Core;

use Themosis\Action\Action;

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
	 * List of file names
	*/
	protected static $names = array();

    /**
     * The WidgetLoader constructor.
    */
	public function __construct()
	{
		Action::listen('widgets_init', $this, 'install')->dispatch();
	}

	/**
	 * Build the path where the class has to scan
	 * the files for adding the WIDGETS.
	 * 
	 * @return bool True. False if not able to add the widget.
	 */
	public static function add()
	{
		static::$path = themosis_path('app').'widgets'.DS;
		return static::append(static::$path);
	}

	/**
	 * Load custom widgets.
	 * 
	 * @return \Themosis\Core\WidgetLoader
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
	 * Install the widgets.
	 * 
	 * @return bool True. False if not installed.
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