<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;

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

    /**
     * The Sidebar constructor.
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
	 * @param string $path The configuration file path.
     * @return void
	 */
	public function set($path)
	{
		$this->datas = include($path);
		$this->event->dispatch();
	}

	/**
	 * Run by the 'after_setup_theme' hook.
	 * Execute the "register_sidebar" function from WP.
     *
     * @return void
	 */
	public function install()
	{
		if (is_array($this->datas) && !empty($this->datas)) {

			foreach ($this->datas as $sidebar) {

				$sidebar['name'] = __($sidebar['name'], THEMOSIS_FRAMEWORK_TEXTDOMAIN);

				if (isset($sidebar['description'])) {

    			    $sidebar['description'] = __($sidebar['description'], THEMOSIS_FRAMEWORK_TEXTDOMAIN);

				}

				register_sidebar($sidebar);

			}
		}

	}
}