<?php
namespace Themosis\Field\Fields;

use Themosis\Core\DataContainer;

abstract class FieldBuilder extends DataContainer{

    /**
     * The field properties.
     *
     * @var array
     */
    protected $properties;

    /**
     * The type of the input handling the value.
     *
     * @var string
     */
    protected $type;

    /**
     * Method to override in the child class to define
     * its input type property.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = '';
    }

    /**
     * Method that return the field input type.
     *
     * @return string
     */
    public function getFieldType()
    {
        return $this->type;
    }

    /**
     * Method that handle the field HTML code for
     * metabox output.
     *
     * @return string
     */
    abstract public function metabox();

    /**
     * Method that handle the field HTML code for
     * page settings output.
     *
     * @return string
     */
    abstract public function page();

} 