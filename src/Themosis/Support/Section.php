<?php

namespace Themosis\Support;

use Themosis\Support\Contracts\SectionInterface;

class Section implements SectionInterface
{
    /**
     * The unique name of the section instance.
     *
     * @var string
     */
    protected $id;

    /**
     * The section view file name.
     *
     * @var string
     */
    protected $view;

    /**
     * The section items (children instances).
     *
     * @var array
     */
    protected $items;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Return the section identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the section view file.
     *
     * @param string $view
     *
     * @return SectionInterface
     */
    public function setView(string $view): SectionInterface
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the section view file.
     *
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * Set section items instances.
     *
     * @param array $items
     *
     * @return SectionInterface
     */
    public function setItems(array $items): SectionInterface
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get section items instances.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
