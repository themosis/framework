<?php
namespace Themosis\Field\Fields;

use Themosis\View\ViewFactory;

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
     * @param array $properties
     * @param ViewFactory $view
     */
    public function __construct(array $properties, ViewFactory $view)
    {
        $this->properties = $properties;
        $this->view = $view;
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
    protected function setLimit()
    {
        $this['limit'] = isset($this['limit']) ? (int)$this['limit'] : 0;
    }

    /**
     * Set a default label title, display text if not defined.
     *
     * @return void
     */
    protected function setTitle()
    {
        $this['title'] = isset($this['title']) ? ucfirst($this['title']) : ucfirst($this['name']);
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

}