<?php
namespace Themosis\Core;

use Themosis\Action\Action;

class WidgetLoader extends Loader
{
	/**
	 * Exclude widgets names
	*/
	protected $excludedWidgets = ['index', 'Index', 'widget', 'Widget'];

	/**
	 * Widgets
	*/
	protected $widgets = [];

    /**
     * The 'widgets_init' event.
     *
     * @var
     */
    protected $event;

	public function __construct($path)
	{
        $this->append($path);

        // Prepare the event.
        $this->event = Action::listen('widgets_init', $this, 'install');

        // Check for valid widgets and load them.
        $this->load();
	}

	/**
	 * Load custom widgets.
	 * 
	 * @return void
	 */
	protected function load()
	{
		foreach ($this->names as $name)
        {
			if (!in_array($name, $this->excludedWidgets))
            {
				$name = $name.'_Widget';
				$this->widgets[] = $name;
			}
		}

        $this->event->dispatch();
	}

	/**
	 * Install the widgets.
	 * 
	 * @return void
	 */
	public function install()
	{
		if (count($this->widgets) > 0)
        {
			foreach ($this->widgets as $widget)
            {
				register_widget($widget);
			}
		}
	}

}