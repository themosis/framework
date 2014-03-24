<?php
namespace Themosis\Configuration;

defined('DS') or die('No direct script access.');

/**
 * Dispatch the right Configuration class
 * depending on its name.
*/

class ConfigFactory
{
	/**
	 * Save a copy of the config name
	*/
	private $name = '';

	/**
	 * Save a copy of the config path
	*/
	private $path = '';

	/**
	 * Save a copy of the path
	*/

	public function __construct($configFile)
	{
		$this->name = $configFile['name'];
		$this->path = $configFile['path'];
	}

	/**
	 * Handle the config class creation depending
	 * of the config name.
	 * 
	 * @param string
	*/
	public function dispatch()
	{
		switch ($this->name) {
			case 'application':
				$application = new Application();
				$application->set($this->path);
				break;

			case 'constants':
			   $constant = new Constant();
			   $constant->set($this->path);
			   break;
            
			case 'errors':
			   $error = new Error();
			   $error->set($this->path);
			   break;
            
			case 'menus':
			   $menu = new Menu();
			   $menu->set($this->path);
			   break;
            
			case 'sidebars':
			   $sidebars = new Sidebar();
			   $sidebars->set($this->path);
			   break;
            
			case 'supports':
			   $supports = new Support();
			   $supports->set($this->path);
			   break;
            
			case 'templates':
			   $template = new Template();
			   $template->set($this->path);
			   break;

			default:
				break;
		}
	}

}