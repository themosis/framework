<?php
/*
 * This class is heavily inspired by the Acorn Composer module.
 * Copyright (c) Roots Software Foundation LLC
 */

namespace Themosis\View;

use Illuminate\Support\Str;
use Illuminate\Support\Fluent;
use Illuminate\View\View;

abstract class Composer
{
    /**
     * View list to compose.
     *
     * @var string[]
     */
    protected static $views;

    /**
     * Current view.
     *
     * @var View
     */
    protected $view;

    /**
     * Current view data
     *
     * @var Fluent
     */
    protected $data;

    /**
     * View list served by this composer
     *
     * @return string|string[]
     */
    public static function views()
    {
        if (static::$views) {
            return static::$views;
        }

        $view = array_slice(explode('\\', static::class), 3);
        $view = array_map([Str::class, 'snake'], $view, array_fill(0, count($view), '-'));
        return implode('/', $view);
    }

    /**
     * Compose the view before rendering.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $this->view = $view;
        $this->data = new Fluent($view->getData());

        $view->with($this->merge());
    }

    /**
     * Data to be and passed to the view before rendering.
     *
     * @return array
     */
    protected function merge()
    {
        return array_merge(
            $this->with(),
            $this->view->getData(),
            $this->override()
        );
    }

    /**
     * Data to be passed to view before rendering
     *
     * @return array
     */
    protected function with()
    {
        return [];
    }

    /**
     * Data to be passed to view before rendering
     *
     * @return array
     */
    protected function override()
    {
        return [];
    }
}