<?php

namespace Themosis\Page;

use Themosis\Hook\IHook;
use Themosis\Page\Contracts\PageInterface;

class Page implements PageInterface
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $menu;

    /**
     * @var string
     */
    protected $cap = 'manage_options';

    /**
     * @var string
     */
    protected $icon = 'dashicons-admin-generic';

    /**
     * @var int
     */
    protected $position = 21;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @var bool
     */
    protected $network = false;

    /**
     * @var IHook
     */
    protected $action;

    public function __construct(IHook $action)
    {
        $this->action = $action;
    }

    /**
     * Return the page slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set the page slug.
     *
     * @param string $slug
     *
     * @return PageInterface
     */
    public function setSlug(string $slug): PageInterface
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the page title.
     *
     * @param string $title
     *
     * @return PageInterface
     */
    public function setTitle(string $title): PageInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the page menu.
     *
     * @return string
     */
    public function getMenu(): string
    {
        return $this->menu;
    }

    /**
     * Set the page menu.
     *
     * @param string $menu
     *
     * @return PageInterface
     */
    public function setMenu(string $menu): PageInterface
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Return the page capability.
     *
     * @return string
     */
    public function getCapability(): string
    {
        return $this->cap;
    }

    /**
     * Set the page capability.
     *
     * @param string $cap
     *
     * @return PageInterface
     */
    public function setCapability(string $cap): PageInterface
    {
        $this->cap = $cap;

        return $this;
    }

    /**
     * Return the page icon.
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Set the page icon.
     *
     * @param string $icon
     *
     * @return PageInterface
     */
    public function setIcon(string $icon): PageInterface
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Return the page position.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set the page position.
     *
     * @param int $position
     *
     * @return PageInterface
     */
    public function setPosition(int $position): PageInterface
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Return the page parent.
     *
     * @return null|string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the page parent.
     *
     * @param string $parent
     *
     * @return PageInterface
     */
    public function setParent(string $parent): PageInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Set the page for network display.
     *
     * @param bool $network
     *
     * @return PageInterface
     */
    public function network(bool $network = true): PageInterface
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Check if the page is for network display.
     *
     * @return bool
     */
    public function isNetwork(): bool
    {
        return $this->network;
    }

    /**
     * Set the page. Display it on the WordPress administration.
     *
     * @return PageInterface
     */
    public function set(): PageInterface
    {
        $hook = $this->isNetwork() ? 'network_admin_menu' : 'admin_menu';

        $this->action->add($hook, [$this, 'build']);

        return $this;
    }

    /**
     * Build the WordPress pages.
     */
    public function build()
    {
        if (is_null($this->getParent())) {
            // Add a top menu page.
            add_menu_page(
                $this->getTitle(),
                $this->getMenu(),
                $this->getCapability(),
                $this->getSlug(),
                [$this, 'render'],
                $this->getIcon(),
                $this->getPosition()
            );
        } else {
            // Add a submenu page.
            add_submenu_page(
                $this->getParent(),
                $this->getTitle(),
                $this->getMenu(),
                $this->getCapability(),
                $this->getSlug(),
                [$this, 'render']
            );
        }
    }

    /**
     * Render/output the page HTML.
     */
    public function render()
    {
        echo "Page";
    }
}
