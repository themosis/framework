<?php

namespace Themosis\Support\Contracts;

interface SectionInterface
{
    /**
     * Return the section identifier.
     */
    public function getId(): string;

    /**
     * Set the section view file.
     */
    public function setView(string $view): SectionInterface;

    /**
     * Set section view data array.
     */
    public function setViewData(array $data): SectionInterface;

    /**
     * Return the view data array.
     */
    public function getViewData(): array;

    /**
     * Get the section view file.
     */
    public function getView(bool $prefixed = false): string;

    /**
     * Set the section theme.
     */
    public function setTheme(string $theme): SectionInterface;

    /**
     * Return the section theme.
     */
    public function getTheme(): string;

    /**
     * Set section items instances.
     */
    public function setItems(array $items): SectionInterface;

    /**
     * Get section items instances.
     */
    public function getItems(): array;

    /**
     * Add an item to the section.
     *
     * @param  mixed  $item
     */
    public function addItem($item): SectionInterface;

    /**
     * Check if the section contains items.
     */
    public function hasItems(): bool;

    /**
     * Set the section title.
     */
    public function setTitle(string $title): SectionInterface;

    /**
     * Return the section title.
     */
    public function getTitle(): string;
}
