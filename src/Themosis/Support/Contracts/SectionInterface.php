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
     * Get the section view file.
     *
     * @return string
     */
    public function getView(): string;

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
}
