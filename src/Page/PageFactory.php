<?php

namespace Themosis\Page;

use Illuminate\Contracts\Container\Container;
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
     * @var IHook
     */
    protected $filter;

    /**
     * @var Factory
     */
    protected $view;

    /**
     * @var FactoryInterface
     */
    protected $validator;

    /**
     * @var string
     */
    protected $prefix = 'page';

    public function __construct(IHook $action, IHook $filter, Factory $view, FactoryInterface $validator)
    {
        $this->action = $action;
        $this->filter = $filter;
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
            ->setTheme('themosis.pages')
            ->setLayout('default')
            ->setView('page');

        $page = new Page($this->action, $this->filter, $view, new PageSettingsRepository(), $this->validator);
        $page->setSlug($slug)
            ->setTitle($title)
            ->setMenu($title);

        // Store page instance within the service container.
        $this->view->getContainer()->instance($this->prefix.'.'.$page->getSlug(), $page);

        return $page;
    }

    /**
     * Return the application service container.
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->view->getContainer();
    }
}
