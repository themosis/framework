<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

class InfiniteField extends FieldBuilder {

    /**
     * Number of registered rows.
     *
     * @var int
     */
    private $rows = 1;

    /**
     * Build an InfiniteField instance.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
        $this->setTitle();
        $this->setRows();
        $this->setLimit();
        $this->fieldType();
    }

    /**
     * Define the limit of rows we can add.
     *
     * @return void
     */
    private function setLimit()
    {
        $this['limit'] = isset($this['limit']) ? (int)$this['limit'] : 0;
    }

    /**
     * Set a default label title, display text if not defined.
     *
     * @return void
     */
    private function setTitle()
    {
        $this['title'] = isset($this['title']) ? ucfirst($this['title']) : ucfirst($this['name']);
    }

    /**
     * Set the number of rows to display.
     *
     * @return int
     */
    private function setRows()
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
     *
     * @return void
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

        return View::make('metabox._themosisInfiniteField', array('field' => $this))->render();
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
}