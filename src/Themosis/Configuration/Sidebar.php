<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class Sidebar implements ConfigInterface
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
	 * Execute the "register_sidebar" function from WP.
	*/
	public function install()
	{
		if (is_array($this->datas) && !empty($this->datas)) {

			foreach ($this->datas as $sidebar) {

				$sidebar['name'] = __($sidebar['name'], THEMOSIS_TEXTDOMAIN);

				if (isset($sidebar['description'])) {

    			    $sidebar['description'] = __($sidebar['description'], THEMOSIS_TEXTDOMAIN);

				}

				register_sidebar($sidebar);

			}
		}

	}
}