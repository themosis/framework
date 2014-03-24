<?php
namespace Themosis\Core;

defined('DS') or die('No direct script access.');

/**
 * Common "interface" for extending the Wordpress
 * 'functions.php' file.
 * Use the Template Design Pattern
*/
abstract class Loader
{	
	/**
	 * Keep a copy of filenames
	*/
	protected static $names = array();

	/**
	 * Scan the directory at the given path and include
	 * all files. Only 1 level iteration.
	 * 
	 * @param string
	 * @return boolean
	*/
	protected static function append($path){

		if (is_dir($path)) {

			$dir = new \DirectoryIterator($path);

			foreach ($dir as $file) {

				if (!$file->isDot() || !$file->isDir()) {

					$file_extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

					if ($file_extension === 'php') {

						static::$names[] = $file->getBasename('.php');

						include_once $file->getPath() . DS . $file->getBasename();

					}

				}

			}

			return true;

		}
		
		return false;

	}
}