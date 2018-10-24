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
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the section title.
     *
     * @param string $title
     *
     * @return SectionInterface
     */
    public function setTitle(string $title): SectionInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the section title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
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
     * @param bool $prefixed
     *
     * @return string
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
     *
     * @param string $theme
     *
     * @return SectionInterface
     */
    public function setTheme(string $theme): SectionInterface
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Return the section theme.
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
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
     * Check if the section contains items.
     *
     * @return bool
     */
    public function hasItems(): bool
    {
        return ! empty($this->items);
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
