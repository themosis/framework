<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class Menu implements ConfigInterface
{
	/**
	 * Save the retrieved datas
	*/
	private $datas;

	/**
	 * The event to dispatch
	*/
	private $event;

	public function __construct()
	{
		$this->event = Action::listen('init', $this, 'install');
	}

	/**
	 * Retrieve and set the datas returned
	 * by the include function using
	 * the given path.
	 * 
	 * @param string
	*/
	public function set($path)
	{
		$this->datas = include($path);
		$this->event->dispatch();
	}

	/**
	 * Run by the 'after_setup_theme' hook.
	 * Execute the "register_nav_menus" function from WP
	*/
	public function install()
	{
		if (is_array($this->datas) && !empty($this->datas)) {

			$locations = array();

			foreach ($this->datas as $slug => $desc) {
				
				$locations[$slug] = __($desc, THEMOSIS_TEXTDOMAIN);

			}

			register_nav_menus($locations);

		}

	}
}