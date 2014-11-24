<?php
namespace Themosis\Core;

class AdminLoader extends Loader implements LoaderInterface
{
	/**
	 * Build the path where the class has to scan
	 * the files for the ADMIN.
	 * 
	 * @return bool True. False if not appended.
	 */
	public static function add()
	{
		$path = themosis_path('admin');
		return static::append($path);
	}

}