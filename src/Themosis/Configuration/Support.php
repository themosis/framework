<?php
namespace Themosis\Configuration;

use Themosis\Action\Action;

class Support
{
	/**
	 * List of theme supports
	*/
	protected $data = [];

	public function __construct(array $data)
	{
        $this->data = $data;
		Action::listen('init', $this, 'install')->dispatch();
	}

	/**
	 * Run by the 'init' hook.
	 * Execute the "add_theme_support" function from WordPress.
	 *
	 * @return void
	 */
	public function install()
	{
		if (is_array($this->data) && !empty($this->data))
		{
			foreach ($this->data as $feature => $value)
			{
				// Allow theme features without options.
				if (is_int($feature))
				{
					add_theme_support($value);
				}
				else
				{
					// Theme features with options.
					add_theme_support($feature, $value);
				}
			}
		}
	}
}