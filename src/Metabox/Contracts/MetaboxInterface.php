<?php

namespace Themosis\Metabox\Contracts;

use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Metabox\Resources\MetaboxResourceInterface;
use Themosis\Support\Contracts\SectionInterface;

interface MetaboxInterface
{
    /**
     * Return the metabox id.
     */
    public function getId(): string;

    /**
     * Set the metabox title.
     */
    public function setTitle(string $title): MetaboxInterface;

    /**
     * Return the metabox title.
     */
    public function getTitle(): string;

    /**
     * Set the metabox screen.
     *
     * @param string|array|\WP_Screen
     * @param  mixed  $screen
     */
    public function setScreen($screen): MetaboxInterface;

    /**
     * Return the metabox screen.
     *
     * @return string|array|\WP_Screen
     */
    public function getScreen();

    /**
     * Set the metabox context.
     */
    public function setContext(string $context): MetaboxInterface;

    /**
     * Get the metabox context.
     */
    public function getContext(): string;

    /**
     * Set the metabox priority.
     */
    public function setPriority(string $priority): MetaboxInterface;

    /**
     * Get the metabox priority.
     */
    public function getPriority(): string;

    /**
     * Set the metabox callback.
     *
     * @param  string|callable  $callback
     */
    public function setCallback($callback): MetaboxInterface;

    /**
     * Return the metabox callback.
     *
     * @return string|callable|array
     */
    public function getCallback();

    /**
     * Set the metabox controller arguments.
     */
    public function setArguments(array $args): MetaboxInterface;

    /**
     * Return the metabox arguments.
     */
    public function getArguments(): array;

    /**
     * Set the metabox layout.
     */
    public function setLayout(string $layout): MetaboxInterface;

    /**
     * Return the metabox layout.
     */
    public function getLayout(): string;

    /**
     * Set the metabox for display.
     */
    public function set(): MetaboxInterface;

    /**
     * Set the metabox resource abstraction layer/manager.
     */
    public function setResource(MetaboxResourceInterface $resource): MetaboxInterface;

    /**
     * Return the metabox resource manager.
     */
    public function getResource(): MetaboxResourceInterface;

    /**
     * Return the metabox as an array resource.
     */
    public function toArray(): array;

    /**
     * Return the metabox as a JSON resource.
     */
    public function toJson(): string;

    /**
     * Set the metabox locale.
     */
    public function setLocale(string $locale): MetaboxInterface;

    /**
     * Return the metabox locale.
     */
    public function getLocale(): string;

    /**
     * Set the metabox prefix.
     */
    public function setPrefix(string $prefix): MetaboxInterface;

    /**
     * Return the metabox prefix.
     */
    public function getPrefix(): string;

    /**
     * Return the metabox fields repository instance.
     */
    public function repository(): FieldsRepositoryInterface;

    /**
     * Add a field or section of fields to the metabox.
     *
     * @param  FieldTypeInterface|SectionInterface  $field
     * @param  SectionInterface  $section
     */
    public function add($field, SectionInterface $section = null): MetaboxInterface;

    /**
     * Return the metabox translations.
     */
    public function getTranslations(): array;

    /**
     * Return the translation if exists.
     */
    public function getTranslation(string $key): string;

    /**
     * Add metabox translation.
     */
    public function addTranslation(string $key, string $translation): MetaboxInterface;

    /**
     * Set the metabox capability.
     */
    public function setCapability(string $cap): MetaboxInterface;

    /**
     * Return the metabox capability.
     */
    public function getCapability(): string;

    /**
     * Set the metabox template.
     *
     * @param  string|array  $template
     */
    public function setTemplate($template, string $screen = 'page'): MetaboxInterface;

    /**
     * Return the metabox template.
     */
    public function getTemplate(): array;
}
