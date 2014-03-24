<?php
namespace Themosis\Asset;

use THFWK_Themosis as Themosis;

defined('DS') or die('No direct script access.');

class CoreAsset extends AssetInterface
{
	/**
	 * The assets directory
	*/
	protected $dir;

	public function __construct($type, $args)
	{
		$this->type = $type;
		$this->args = $args;
		$this->dir = plugins_url(Themosis::getDirName()).DS.'src'.DS.'Themosis'.DS.'_assets';
	}

	public function registerScript()
	{
		$path = $this->parsePath($this->args['path']);
		$path = $this->isExternal($path);

		$footer = (is_bool($this->args['mixed'])) ? $this->args['mixed'] : false;
		$version = (is_string($this->args['version'])) ? $this->args['version'] : false;

		wp_enqueue_script($this->args['handle'], $path, $this->args['deps'], $version, $footer);
	}

	public function registerStyle()
	{
		$path = $this->parsePath($this->args['path']);
		$path = $this->isExternal($path);

		$media = (is_string($this->args['mixed'])) ? $this->args['mixed'] : 'all';
		$version = (is_string($this->args['version'])) ? $this->args['version'] : false;

		wp_enqueue_style($this->args['handle'], $path, $this->args['deps'], $version, $media);
	}
}