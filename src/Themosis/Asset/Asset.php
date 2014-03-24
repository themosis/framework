<?php
namespace Themosis\Asset;

defined('DS') or die('No direct script access.');

class Asset
{
	/**
	 * Allowed areas
	*/
	protected $allowedAreas = array('admin', 'login');

	/**
	 * AssetFactory
	*/
	protected $factory;

	public function __construct($type, $args)
	{	
		$this->factory = new AssetFactory(false, $type, $args);
	}

	/**
	 * Add an asset in the FRONTEND or BACKEND or LOGIN
	 * Nearly give default WP parameters: the asset handle,
	 * its path, its dependencies and if it has to be loaded in the
	 * footer.
	 * 
	 * NOTE : The path is relative to the "assets" folder situated
	 * in the "app" folder.
	 * By default, add the asset in the FRONTEND
	 * 
	 * @param string
	 * @param string
	 * @param array (optional)
	 * @param mixed (optional)
	*/
	public static function add($handle, $path, $deps = array(), $version = '1.0', $mixed = null)
	{
		if (is_string($handle) && is_string($path)) {

			// Define if we use a script or a style file
			$type = (pathinfo($path, PATHINFO_EXTENSION) == 'css') ? 'style' : 'script';
			// Group all parameters
			$args = compact('handle', 'path', 'deps', 'version', 'mixed');

			// Build the Asset for the Front-End by default
			return new static($type, $args);

		} else {

			throw new AssetException("Invalid parameters for Asset::add method.");

		}
	}

	/**
	 * Allow the developer to define where
	 * to load the asset. Only 'admin' or 'login'
	 * are accepted.
	 * 
	 * @param string
	*/
	public function to($area)
	{
		if (is_string($area) && in_array($area, $this->allowedAreas)) {

			$this->factory->setArea($area);

		} else {
			
			throw new AssetException("Invalid parameters for Asset->to() method.");

		}
	}
}