<?php

namespace Themosis\Support\Contracts;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

interface UIContainerInterface
{
    /**
     * Return the container theme.
     */
    public function getTheme(): string;

    /**
     * Set the container theme.
     */
    public function setTheme(string $theme): UIContainerInterface;

    /**
     * Return the container layout.
     */
    public function getLayout(): string;

    /**
     * Define a layout/view to use for the container (without a namespace).
     */
    public function setLayout(string $layout): UIContainerInterface;

    /**
     * Define a view to use for the container.
     */
    public function setView(string $view): UIContainerInterface;

    /**
     * Return the container view path.
     */
    public function getViewPath(): string;

    /**
     * Return the container view instance.
     */
    public function getView(): View;

    /**
     * Return the container view factory.
     */
    public function factory(): Factory;
}
