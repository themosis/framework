<?php

namespace Themosis\Support\Contracts;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

interface UIContainerInterface
{
    /**
     * Return the container theme.
     *
     * @return string
     */
    public function getTheme(): string;

    /**
     * Set the container theme.
     *
     * @param string $theme
     *
     * @return UIContainerInterface
     */
    public function setTheme(string $theme): UIContainerInterface;

    /**
     * Return the container layout.
     *
     * @return string
     */
    public function getLayout(): string;

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
     * Return the container view path.
     *
     * @return string
     */
    public function getViewPath(): string;

    /**
     * Return the container view instance.
     *
     * @return View
     */
    public function getView(): View;

    /**
     * Return the container view factory.
     *
     * @return Factory
     */
    public function factory(): Factory;
}
