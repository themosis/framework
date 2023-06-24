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
     * The section title.
     *
     * @var string
     */
    protected $title;

    /**
     * The section view file name.
     *
     * @var string
     */
    protected $view = '';

    /**
     * The section theme.
     *
     * @var string
     */
    protected $theme = '';

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

    public function __construct(string $id, string $title = '', array $items = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->items = $items;
    }

    /**
     * Return the section identifier.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the section title.
     */
    public function setTitle(string $title): SectionInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the section title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the section view file.
     */
    public function setView(string $view): SectionInterface
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the section view file.
     */
    public function getView(bool $prefixed = false): string
    {
        if ($prefixed) {
            return $this->getTheme().'.'.$this->view;
        }

        return $this->view;
    }

    /**
     * Set the section theme.
     */
    public function setTheme(string $theme): SectionInterface
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Return the section theme.
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Set section view data array.
     */
    public function setViewData(array $data): SectionInterface
    {
        $this->data = $data;

        // Add default variables to the section view.
        // We provide a copy of the section instance
        // so we can iterate over its items from the view.
        $this->data = array_merge($data, [
            '__section' => $this,
        ]);

        return $this;
    }

    /**
     * Return the view data array.
     */
    public function getViewData(): array
    {
        return $this->data;
    }

    /**
     * Set section items instances.
     */
    public function setItems(array $items): SectionInterface
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get section items instances.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Add an item to the section.
     *
     * @param  mixed  $item
     */
    public function addItem($item): SectionInterface
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Check if the section contains items.
     */
    public function hasItems(): bool
    {
        return ! empty($this->items);
    }

    /**
     * Get current item.
     */
    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    /**
     * Move to the next item.
     */
    public function next(): void
    {
        $this->position++;
    }

    /**
     * Get current key position.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Check if item is valid or exists.
     */
    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    /**
     * Reset to start position.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Return the number of items.
     */
    public function count(): int
    {
        return count($this->items);
    }
}
