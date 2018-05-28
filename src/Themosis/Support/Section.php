<?php

namespace Themosis\Support;

use Countable;
use Iterator;
use Themosis\Support\Contracts\SectionInterface;

class Section implements SectionInterface, Iterator, Countable
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

    /**
     * Section's view data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Default iterator position.
     *
     * @var int
     */
    private $position = 0;

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
     * Set section view data array.
     *
     * @param array $data
     *
     * @return SectionInterface
     */
    public function setViewData(array $data): SectionInterface
    {
        $this->data = $data;

        // Add default variables to the section view.
        // We provide a copy of the section instance
        // so we can iterate over its items from the view.
        $this->data = array_merge($data, [
            '__section' => $this
        ]);

        return $this;
    }

    /**
     * Return the view data array.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return $this->data;
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

    /**
     * Add an item to the section.
     *
     * @param mixed $item
     *
     * @return SectionInterface
     */
    public function addItem($item): SectionInterface
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get current item.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->items[$this->position];
    }

    /**
     * Move to the next item.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Get current key position.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Check if item is valid or exists.
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    /**
     * Reset to start position.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Return the number of items.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }
}
