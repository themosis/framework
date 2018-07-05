<?php

namespace Themosis\Page;

use Illuminate\Contracts\Validation\Factory as FactoryInterface;
use Illuminate\Contracts\View\Factory;
use Themosis\Hook\IHook;
use Themosis\Page\Contracts\PageFactoryInterface;
use Themosis\Page\Contracts\PageInterface;

class PageFactory implements PageFactoryInterface
{
    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var Factory
     */
    protected $view;

    /**
     * @var FactoryInterface
     */
    protected $validator;

    public function __construct(IHook $action, Factory $view, FactoryInterface $validator)
    {
        $this->action = $action;
        $this->view = $view;
        $this->validator = $validator;
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
        $view = (new PageView($this->view))
            ->setTheme('themosis')
            ->setLayout('default')
            ->setView('page');

        return (new Page($this->action, $view, new PageSettingsRepository(), $this->validator))
            ->setSlug($slug)
            ->setTitle($title)
            ->setMenu($title);
    }
}
