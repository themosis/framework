<?php

namespace Themosis\Page\Contracts;

use Illuminate\Contracts\Container\Container;
use Themosis\Support\Contracts\UIContainerInterface;

interface PageInterface
{
    /**
     * Return the page slug.
     */
    public function getSlug(): string;

    /**
     * Set the page slug.
     */
    public function setSlug(string $slug): PageInterface;

    /**
     * Return the page title.
     */
    public function getTitle(): string;

    /**
     * Set the page title.
     */
    public function setTitle(string $title): PageInterface;

    /**
     * Return the page menu title.
     */
    public function getMenu(): string;

    /**
     * Set the page menu title.
     */
    public function setMenu(string $menu): PageInterface;

    /**
     * Get the page capability.
     */
    public function getCapability(): string;

    /**
     * Set the page capability.
     */
    public function setCapability(string $cap): PageInterface;

    /**
     * Return the page icon.
     */
    public function getIcon(): string;

    /**
     * Set the page icon.
     */
    public function setIcon(string $icon): PageInterface;

    /**
     * Return the page position.
     */
    public function getPosition(): int;

    /**
     * Set the page position.
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
     */
    public function setParent(string $parent): PageInterface;

    /**
     * Check if current page has a parent.
     */
    public function hasParent(): bool;

    /**
     * Return the page URL.
     */
    public function getUrl(array $queryArgs = []): string;

    /**
     * Set the global page property show in rest.
     * Automatically set all attached settings to this global value.
     * Each setting can override individually this option using an
     * option "show_in_rest".
     *
     * @param  bool  $show
     */
    public function showInRest($show = true): PageInterface;

    /**
     * Return the page show in rest property value.
     */
    public function isShownInRest(): bool;

    /**
     * Set the page for network display.
     */
    public function network(bool $network = true): PageInterface;

    /**
     * Check if the page is for network rendering.
     */
    public function isNetwork(): bool;

    /**
     * Set the page. Display it on the WordPress administration.
     */
    public function set(): PageInterface;

    /**
     * Return the page ui instance.
     */
    public function ui(): UIContainerInterface;

    /**
     * Add data to page ui instance.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     */
    public function with($key, $value = null): PageInterface;

    /**
     * Return the page settings repository.
     */
    public function repository(): SettingsRepositoryInterface;

    /**
     * Add sections to the page.
     */
    public function addSections(array $sections): PageInterface;

    /**
     * Add settings to the page.
     *
     * @param  string|array  $section
     */
    public function addSettings($section, array $settings = []): PageInterface;

    /**
     * Return the page prefix.
     */
    public function getPrefix(): string;

    /**
     * Set the page settings name prefix.
     */
    public function setPrefix(string $prefix): PageInterface;

    /**
     * Set the page view path.
     */
    public function setView(string $name, bool $useShortPath = false): PageInterface;

    /**
     * Register page routes.
     *
     * @param  string|callable  $callback
     */
    public function route(string $action, $callback, string $method = 'get', string $title = ''): PageInterface;

    /**
     * Return the service container instance.
     */
    public function getContainer(): Container;

    /**
     * Return the prefixed action name for POST requests.
     */
    public function getAction(string $action): string;

    public function getPostUrl(): string;
}
