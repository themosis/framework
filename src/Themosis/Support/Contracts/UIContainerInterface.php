<?php

namespace Themosis\Support\Contracts;

use Illuminate\Contracts\View\View;

interface UIContainerInterface
{
    /**
     * Return the container layout.
     *
     * @return String
     */
    public function getLayout(): String;

    /**
     * Define a layout/view to use for the container (without a namespace).
     *
     * @param string $layout
     *
     * @return UIContainerInterface
     */
    public function setLayout(string $layout): UIContainerInterface;

    /**
     * Define a view to use for the container.
     *
     * @param string $view
     *
     * @return UIContainerInterface
     */
    public function setView(string $view): UIContainerInterface;

    /**
     * Return the container view instance.
     *
     * @return View
     */
    public function getView(): View;
}
