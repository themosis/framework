<?php

namespace Themosis\Page\Contracts;

use Illuminate\Contracts\Container\Container;

interface PageFactoryInterface
{
    /**
     * Build a new page instance.
     *
     * @param string $slug  The page slug.
     * @param string $title The page title.
     *
     * @return PageInterface
     */
    public function make(string $slug, string $title): PageInterface;

    /**
     * Return the application service container.
     *
     * @return Container
     */
    public function getContainer(): Container;
}
