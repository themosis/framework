<?php
namespace Themosis\Asset;

defined('DS') or die('No direct script access.');

class FrontAsset extends AssetInterface
{
	/**
	 * The assets directory
	*/
	protected $dir;

	public function __construct($type, $args)
	{
		$this->type = $type;
		$this->args = $args;
		$this->dir = get_template_directory_uri().DS.'app'.DS.'assets';
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

		wp_enqueue_style($this->args['handle'], $path, $this->args['deps'], $version, $media); // MEDIA AS A LAST PARAMETER
	}

}