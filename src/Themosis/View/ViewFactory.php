<?php
namespace Themosis\View;

class ViewFactory {

    /**
     * Define a ViewFactory instance.
     */
    public function __construct()
    {

    }

    /**
     * Build a view instance.
     *
     * @return \Themosis\View\View
     */
    public function make()
    {
        $view = new View();

        return $view;
    }

} 