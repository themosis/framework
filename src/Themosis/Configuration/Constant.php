<?php
namespace Themosis\Configuration;

class Constant extends ConfigTemplate
{	
	/**
	 * Save the retrieved datas
	*/
	protected static $datas = array();

	/**
	 * Load all plugin's constants
     *
     * @return void
	*/
	public static function load()
	{
		if (isset(static::$datas) && !empty(static::$datas)) {
			
			foreach (static::$datas as $name => $value) {
				
				$name = strtoupper($name);

				defined($name) ? $name : define($name, $value);

			}

		}
	}

}