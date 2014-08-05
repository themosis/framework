<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;

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

    /**
     * The Menu constructor.
     */
	public function __construct()
	{
		$this->event = Action::listen('init', $this, 'install');
	}

    /**
     * Retrieve and set the datas returned
     * by the include function using
     * the given path.
     *
     * @param string $path The config file path.
     * @return void
     */
	public function set($path)
	{
		$this->datas = include($path);
		$this->event->dispatch();
	}

	/**
	 * Run by the 'init' hook.
	 * Execute the "register_nav_menus" function from WP
     *
     * @return void
	 */
	public function install()
	{
		if (is_array($this->datas) && !empty($this->datas)) {

			$locations = array();

			foreach ($this->datas as $slug => $desc) {
				
				$locations[$slug] = __($desc, THEMOSIS_FRAMEWORK_TEXTDOMAIN);

			}

			register_nav_menus($locations);

		}

	}
}