<?php
namespace Themosis\Asset;

defined('DS') or die('No direct script access.');

abstract class AssetInterface
{	
	/**
	 * Style or script type
	*/
	protected $type;

	/**
	 * Asset parameters
	*/
	protected $args;

	/**
	 * Handle the registering of a script asset
	*/
	public abstract function registerScript();

	/**
	 * Handle the registering of a style asset
	*/
	public abstract function registerStyle();

	/**
	 * Sanitized the given asset path
	 * Check if there is a DirectorySeparator
	 * at the beginning of the path.
	 * 
	 * @param string $path The raw asset path.
	 * @return string The sanitized asset path.
	 */
	protected function parsePath($path)
	{
		if(substr($path, 0, 1) !== '/' && substr($path, 0, 4) !== 'http'){
			$path = '/'.$path;
		}

		return $path;
	}

	/**
	 * Check if is an asset from a CDN. 
	 * Contains http or similar pattens in its
	 * path.
	 * 
	 * @param string $path The asset path.
	 * @return string The asset path.
	 */
	protected function isExternal($path)
	{
		// Check for '//'
		if (strpos ($path, '//') !== false) {
			return $path;

		// Check for 'http://' or 'https://'
		} elseif (strpos($path, 'http://') !== false || strpos($path, 'https://') !== false) {
			return $path;
		}

		// Else return the relative path
		return $this->dir.$path;
	}

	/**
	 * Return the asset type.
     *
     * @return string The asset type: 'style' or 'script'
	 */
	public function getType()
	{
		return $this->type;
	}
}