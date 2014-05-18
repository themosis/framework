<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

abstract class Viewer
{
	/**
	 * Parse the given path and check if we need to include
	 * a file from a subdirectory or not. Convert all "." into
	 * directory separator "/".
	 *
	 * @param string $path The view file raw path.
	 * @return string The converted path.
	 */
	protected static function parsePath($path)
	{
		if (strpos($path, '.') !== false) {

			$path = str_replace('.', DS, $path);			

		} else {
			
			$path = trim($path);

		}

		return (string)$path;
	}

    /**
     * Handle the logic for processing the new view.
     *
     * @param string $path The view file real path.
     * @param array $datas The datas to pass to the view.
     * @throws ViewException
     * @return \Themosis\View\Viewer
     */
	protected static function parse($path, array $datas = array())
	{
		// Check if a file using the Scout engine exists first.
		// If not check for a standard view file.
		if (file_exists($file = $path.SCOUT_EXT)) {

			return new static($file, $datas, true);

		} else if (file_exists($file = $path.EXT)) {

			return new static($file, $datas);

		} else {
			throw new ViewException("Invalid view path or the view doesn't exist.");
		}
	}
	
}