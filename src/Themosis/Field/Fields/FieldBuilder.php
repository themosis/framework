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
     * Method that handle the field HTML code for
     * metabox output.
     *
     * @return mixed
     */
    abstract public function metabox();

} 