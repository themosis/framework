<?php

namespace Themosis\Support\Contracts;

interface SectionInterface
{
    /**
     * Return the section identifier.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Set the section view file.
     *
     * @param string $view
     *
     * @return SectionInterface
     */
    public function setView(string $view): SectionInterface;

    /**
     * Set section view data array.
     *
     * @param array $data
     *
     * @return SectionInterface
     */
    public function setViewData(array $data): SectionInterface;

    /**
     * Return the view data array.
     *
     * @return array
     */
    public function getViewData(): array;

    /**
     * Get the section view file.
     *
     * @param bool $prefixed
     *
     * @return string
     */
    public function getView(bool $prefixed = false): string;

    /**
     * Set the section theme.
     *
     * @param string $theme
     *
     * @return SectionInterface
     */
    public function setTheme(string $theme): SectionInterface;

    /**
     * Return the section theme.
     *
     * @return string
     */
    public function getTheme(): string;

    /**
     * Set section items instances.
     *
     * @param array $items
     *
     * @return SectionInterface
     */
    public function setItems(array $items): SectionInterface;

    /**
     * Get section items instances.
     *
     * @return array
     */
    public function getItems(): array;

    /**
     * Add an item to the section.
     *
     * @param mixed $item
     *
     * @return SectionInterface
     */
    public function addItem($item): SectionInterface;

    /**
     * Check if the section contains items.
     *
     * @return bool
     */
    public function hasItems(): bool;

    /**
     * Set the section title.
     *
     * @param string $title
     *
     * @return SectionInterface
     */
    public function setTitle(string $title): SectionInterface;

    /**
     * Return the section title.
     *
     * @return string
     */
    public function getTitle(): string;
}
