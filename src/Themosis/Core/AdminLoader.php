<?php
namespace Themosis\Core;

defined('DS') or die('No direct script access.');

class AdminLoader extends Loader implements LoaderInterface
{
	/**
	 * Build the path where the class has to scan
	 * the files for the ADMIN.
	 * 
	 * @return boolean
	*/
	public static function add()
	{
		$path = themosis_path('datas').'admin'.DS;
		return static::append($path);
	}

}