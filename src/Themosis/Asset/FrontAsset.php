<?php
namespace Themosis\Asset;

defined('DS') or die('No direct script access.');

class FrontAsset extends AssetInterface
{
	/**
	 * The assets directory
	*/
	protected $dir;

    /**
     * The FrontAsset constructor.
     *
     * @param string $type The asset type.
     * @param array $args The asset arguments.
     */
	public function __construct($type, array $args)
	{
		$this->type = $type;
		$this->args = $args;
		$this->dir = get_template_directory_uri().DS.'app'.DS.'assets';
	}

    /**
     * Register a javascript file for the application.
     *
     * @return void
     */
	public function registerScript()
	{
		$path = $this->parsePath($this->args['path']);
		$path = $this->isExternal($path);

		$footer = (is_bool($this->args['mixed'])) ? $this->args['mixed'] : false;
		$version = (is_string($this->args['version'])) ? $this->args['version'] : false;

		wp_enqueue_script($this->args['handle'], $path, $this->args['deps'], $version, $footer);
	}

    /**
     * Register a stylesheet file for the application.
     *
     * @return void
     */
	public function registerStyle()
	{
		$path = $this->parsePath($this->args['path']);
		$path = $this->isExternal($path);

		$media = (is_string($this->args['mixed'])) ? $this->args['mixed'] : 'all';
		$version = (is_string($this->args['version'])) ? $this->args['version'] : false;

		wp_enqueue_style($this->args['handle'], $path, $this->args['deps'], $version, $media); // MEDIA AS A LAST PARAMETER
	}

}