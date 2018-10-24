<?php

namespace Themosis\Page\Contracts;

use Illuminate\Contracts\Container\Container;
use Themosis\Support\Contracts\UIContainerInterface;

interface PageInterface
{
    /**
     * Return the page slug.
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Set the page slug.
     *
     * @param string $slug
     *
     * @return PageInterface
     */
    public function setSlug(string $slug): PageInterface;

    /**
     * Return the page title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set the page title.
     *
     * @param string $title
     *
     * @return PageInterface
     */
    public function setTitle(string $title): PageInterface;

    /**
     * Return the page menu title.
     *
     * @return string
     */
    public function getMenu(): string;

    /**
     * Set the page menu title.
     *
     * @param string $menu
     *
     * @return PageInterface
     */
    public function setMenu(string $menu): PageInterface;

    /**
     * Get the page capability.
     *
     * @return string
     */
    public function getCapability(): string;

    /**
     * Set the page capability.
     *
     * @param string $cap
     *
     * @return PageInterface
     */
    public function setCapability(string $cap): PageInterface;

    /**
     * Return the page icon.
     *
     * @return string
     */
    public function getIcon(): string;

    /**
     * Set the page icon.
     *
     * @param string $icon
     *
     * @return PageInterface
     */
    public function setIcon(string $icon): PageInterface;

    /**
     * Return the page position.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Set the page position.
     *
     * @param int $position
     *
     * @return PageInterface
     */
    public function setPosition(int $position): PageInterface;

    /**
     * Return the page parent.
     *
     * @return string|null
     */
    public function getParent();

    /**
     * Set the page parent.
     *
     * @param string $parent
     *
     * @return PageInterface
     */
    public function setParent(string $parent): PageInterface;

    /**
     * Check if current page has a parent.
     *
     * @return bool
     */
    public function hasParent(): bool;

    /**
     * Set the global page property show in rest.
     * Automatically set all attached settings to this global value.
     * Each setting can override individually this option using an
     * option "show_in_rest".
     *
     * @param bool $show
     *
     * @return PageInterface
     */
    public function showInRest($show = true): PageInterface;

    /**
     * Return the page show in rest property value.
     *
     * @return bool
     */
    public function isShownInRest(): bool;

    /**
     * Set the page for network display.
     *
     * @param bool $network
     *
     * @return PageInterface
     */
    public function network(bool $network = true): PageInterface;

    /**
     * Check if the page is for network rendering.
     *
     * @return bool
     */
    public function isNetwork(): bool;

    /**
     * Set the page. Display it on the WordPress administration.
     *
     * @return PageInterface
     */
    public function set(): PageInterface;

    /**
     * Return the page ui instance.
     *
     * @return UIContainerInterface
     */
    public function ui(): UIContainerInterface;

    /**
     * Add data to page ui instance.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return PageInterface
     */
    public function with($key, $value = null): PageInterface;

    /**
     * Return the page settings repository.
     *
     * @return SettingsRepositoryInterface
     */
    public function repository(): SettingsRepositoryInterface;

    /**
     * Add sections to the page.
     *
     * @param array $sections
     *
     * @return PageInterface
     */
    public function addSections(array $sections): PageInterface;

    /**
     * Add settings to the page.
     *
     * @param string|array $section
     * @param array        $settings
     *
     * @return PageInterface
     */
    public function addSettings($section, array $settings = []): PageInterface;

    /**
     * Return the page prefix.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Set the page settings name prefix.
     *
     * @param string $prefix
     *
     * @return PageInterface
     */
    public function setPrefix(string $prefix): PageInterface;

    /**
     * Set the page view path.
     *
     * @param string $name
     * @param bool   $useShortPath
     *
     * @return PageInterface
     */
    public function setView(string $name, bool $useShortPath = false): PageInterface;

    /**
     * Register page routes.
     *
     * @param string          $action
     * @param string|callable $callback
     * @param string          $method
     * @param string          $title
     *
     * @return PageInterface
     */
    public function route(string $action, $callback, string $method = 'get', string $title = ''): PageInterface;

    /**
     * Return the service container instance.
     *
     * @return Container
     */
    public function getContainer(): Container;

    /**
     * Return the prefixed action name for POST requests.
     *
     * @param string $action
     *
     * @return string
     */
    public function getAction(string $action): string;
}
