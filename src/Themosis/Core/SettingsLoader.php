<?php
namespace Themosis\Core;

defined('DS') or die('No direct script access.');

class SettingsLoader extends Loader implements LoaderInterface
{
	/**
	 * Build the path where the class has to scan
	 * the files for the ADMIN.
	 * 
	 * @return boolean
	*/
	public static function add()
	{
		$path = themosis_path('sys').'Settings'.DS;
		return static::append($path);
	}

}