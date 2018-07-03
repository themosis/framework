<?php

namespace Themosis\Page;

use Themosis\Hook\IHook;
use Themosis\Page\Contracts\PageFactoryInterface;
use Themosis\Page\Contracts\PageInterface;

class PageFactory implements PageFactoryInterface
{
    /**
     * @var IHook
     */
    protected $action;

    public function __construct(IHook $action)
    {
        $this->action = $action;
    }

    /**
     * Make a new page instance.
     *
     * @param string $slug
     * @param string $title
     *
     * @return PageInterface
     */
    public function make(string $slug, string $title): PageInterface
    {
        return (new Page($this->action))
            ->setSlug($slug)
            ->setTitle($title)
            ->setMenu($title);
    }
}
