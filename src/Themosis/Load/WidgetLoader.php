<?php

namespace Themosis\Load;

use Themosis\Hook\IHook;

class WidgetLoader extends Load
{
    /**
     * List of excluded widgets names.
     *
     * @var array
     */
    protected $excludedWidgets = ['widget', 'Widget'];

    /**
     * The loaded widgets.
     *
     * @var array
     */
    protected $widgets = [];

    /**
     * @var IHook
     */
    protected $filter;

    /**
     * WidgetLoader constructor.
     *
     * @param IHook $filter
     * @param array $paths
     */
    public function __construct(IHook $filter, array $paths = [])
    {
        parent::__construct($paths);
        $this->filter = $filter;
    }

    /**
     * Load the widgets.
     *
     * @return \Themosis\Load\ILoader
     */
    public function load()
    {
        parent::load();

        foreach ($this->files as $file) {
            if (!in_array($file['name'], $this->excludedWidgets)) {
                $name = $file['name'].'_Widget';
                $this->widgets[] = $name;
            }
        }

        $this->filter->add('widgets_init', [$this, 'install']);

        return $this;
    }

    /**
     * Register the widgets.
     */
    public function install()
    {
        if (count($this->widgets) > 0) {
            foreach ($this->widgets as $widget) {
                register_widget($widget);
            }
        }
    }

    /**
     * Return a list of allowed widgets.
     * 
     * @return array
     */
    public function getWidgets()
    {
        return $this->widgets;
    }
}
