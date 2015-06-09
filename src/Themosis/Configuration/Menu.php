<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;

class Menu
{
	/**
	 * Save the menus list
     *
     * @var array
	*/
	protected $data = [];

	public function __construct(array $data)
	{
        $this->data = $data;
		Action::listen('init', $this, 'install')->dispatch();
	}

	/**
	 * Run by the 'init' hook.
	 * Execute the "register_nav_menus" function from WordPress
     *
     * @return void
	 */
	public function install()
	{
		if (is_array($this->data) && !empty($this->data))
        {
			$locations = [];

			foreach ($this->data as $slug => $desc)
            {
				$locations[$slug] = $desc;
			}

			register_nav_menus($locations);
		}
	}
}