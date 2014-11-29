<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

class CollectionField extends FieldBuilder {

    /**
     * Define a collection field instance.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        parent::__construct($properties);

        $this->fieldType();
    }

    /**
     * Method to override that defined the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = 'collection';
    }

    /**
     * Method that handle the field HTML code for
     * metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        return View::make('metabox._themosisCollectionField', array('field' => $this))->render();
    }

    /**
     * Method that handle the field HTML code for
     * page settings output.
     *
     * @return string
     */
    public function page()
    {
        return $this->metabox();
    }
}