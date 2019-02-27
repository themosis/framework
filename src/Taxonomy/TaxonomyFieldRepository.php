<?php

namespace Themosis\Taxonomy;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Taxonomy\Exceptions\TaxonomyFieldNotFoundException;

class TaxonomyFieldRepository implements \Iterator
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * List of taxonomy fields.
     *
     * @var array
     */
    private $fields = [];

    /**
     * Add a field to the taxonomy.
     *
     * @param FieldTypeInterface|array $field
     */
    public function add($field)
    {
        if (is_array($field)) {
            foreach ($field as $item) {
                $this->add($item);
            }
        } else {
            $this->fields[] = $field;
        }
    }

    /**
     * Return all attached fields.
     *
     * @return array
     */
    public function all()
    {
        return $this->fields;
    }

    /**
     * Return a field instance by name.
     *
     * @param string $name
     *
     * @throws TaxonomyFieldNotFoundException
     *
     * @return FieldTypeInterface
     */
    public function getFieldByName(string $name)
    {
        $found = array_filter($this->fields, function ($field) use ($name) {
            /** @var FieldTypeInterface $field */
            return $name === $field->getBaseName();
        });

        if (! empty($found)) {
            return array_pop($found);
        }

        throw new TaxonomyFieldNotFoundException("Taxonomy field with a name of {$name} not found.");
    }

    /**
     * Return the current element
     *
     * @link https://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     *
     * @since 5.0.0
     */
    public function current()
    {
        return $this->fields[$this->position];
    }

    /**
     * Move forward to next element
     *
     * @link https://php.net/manual/en/iterator.next.php
     * @since 5.0.0
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Return the key of the current element
     *
     * @link https://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure.
     *
     * @since 5.0.0
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     *
     * @link https://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     *              Returns true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function valid()
    {
        return isset($this->fields[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link https://php.net/manual/en/iterator.rewind.php
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
