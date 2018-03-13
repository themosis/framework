<?php

namespace Themosis\Field\Fields;

use Illuminate\View\Factory;

class InfiniteField extends FieldBuilder implements IField
{
    /**
     * Number of registered rows.
     *
     * @var int
     */
    protected $rows = 1;

    /**
     * Build an InfiniteField instance.
     *
     * @param array                    $properties
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(array $properties, Factory $view)
    {
        parent::__construct($properties, $view);
        $this->setRows();
        $this->setLimit();
        $this->fieldType();
    }

    /**
     * Set the number of rows to display.
     *
     * @return int
     */
    protected function setRows()
    {
        $this->rows = (is_array($this['value']) && !empty($this['value'])) ? count($this['value']) : 1;
    }

    /**
     * Return the numbers of rows to display.
     *
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Define the input type that handle the data.
     */
    protected function fieldType()
    {
        $this->type = 'infinite';
    }

    /**
     * Method that handle the field HTML code for
     * metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        // Check rows number.
        $this->setRows();

        return $this->view->make('metabox._themosisInfiniteField', ['field' => $this])->render();
    }

    /**
     * Handle the field HTML code for the
     * Settings API output.
     *
     * @return string
     */
    public function page()
    {
        return $this->metabox();
    }

    /**
     * Handle the HTML code for user output.
     *
     * @return string
     */
    public function user()
    {
        return $this->metabox();
    }

    /**
     * Handle the HTML code for taxonomy output.
     *
     * @return string
     */
    public function taxonomy()
    {
        return $this->metabox();
    }
}
