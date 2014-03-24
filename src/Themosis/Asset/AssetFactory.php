<?php
namespace Themosis\Asset;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class AssetFactory
{
	/**
	 * Where to load the asset - By default 
	 * on the front-end.
	*/
	private $area = 'front';

	/**
	 * Key of the asset
	*/
	private $key;

	/**
	 * Whether or not handle an asset for the
	 * core framework.
	*/
	private $isCore;

	/**
	 * Keep a reference to all assets factories
	*/
	private static $instances;

	public function __construct($isCore, $type, $args)
	{
		$this->isCore = $isCore;
		$this->key = strtolower(trim($args['handle']));

		// Assign a dedicated class to core assets.
		if ($this->isCore) {
			$this->assetHandler = new CoreAsset($type, $args);
		} else {
			$this->assetHandler = new FrontAsset($type, $args);
		}

		static::$instances[$this->area][$this->key] = $this;

		Action::listen('wp_enqueue_scripts', $this, 'install')->dispatch();
		Action::listen('admin_enqueue_scripts', $this, 'install')->dispatch();
		Action::listen('login_enqueue_scripts', $this, 'install')->dispatch();			
	}

	/**
	 * Install the appropriate asset
	 * depending of its area.
	*/
	public static function install()
	{
		$from = current_filter();

		switch ($from) {
			case 'wp_enqueue_scripts':
				
				if (isset(static::$instances['front']) && !empty(static::$instances['front'])) {

					foreach (static::$instances['front'] as $asset) {
						
						static::register($asset);

					}

				}

				break;

			case 'admin_enqueue_scripts':

				if (isset(static::$instances['admin']) && !empty(static::$instances['admin'])) {

					foreach (static::$instances['admin'] as $asset) {

						static::register($asset);

					}

				}
				
				break;

			case 'login_enqueue_scripts':
				
				if (isset(static::$instances['login']) && !empty(static::$instances['login'])) {

					foreach (static::$instances['login'] as $asset) {

						static::register($asset);

					}

				}

				break;
		}

	}

	/**
	 * Register the script or the style asset file.
	 * 
	 * @param object
	*/
	private static function register(AssetFactory $asset)
	{
		if ($asset->assetHandler->getType() === 'script') {

			$asset->assetHandler->registerScript();

		} else {

			$asset->assetHandler->registerStyle();

		}
	}

	/**
	 * Set the area of the asset
	 * 
	 * @param string
	*/
	public function setArea($area)
	{
		$this->area = $area;
		$this->orderInstances();
	}

	/**
	 * Manipulate the static::$instances variable
	 * in order to separate each asset in its area.
	*/
	private function orderInstances()
	{
		if (array_key_exists($this->key, static::$instances['front'])) {
			
			$instance = static::$instances['front'][$this->key];
			unset(static::$instances['front'][$this->key]);

			static::$instances[$this->area][$instance->key] = $instance;

		}
	}
}