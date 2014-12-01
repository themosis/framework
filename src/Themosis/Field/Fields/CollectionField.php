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

        $this->setType();
        $this->setLimit();
        $this->fieldType();
    }

    /**
     * Set the type data of the media to insert.
     * If no type is defined, default to 'image'.
     *
     * @return void
     */
    private function setType()
    {
        $allowed = array('image', 'application', 'video', 'audio');

        if(isset($this['type']) && !in_array($this['type'], $allowed)){
            $this['type'] = 'image';
        } elseif(!isset($this['type'])){
            $this['type'] = 'image';
        }
    }

    /**
     * Define the limit of media files we can add.
     *
     * @return void
     */
    private function setLimit()
    {
        $this['limit'] = isset($this['limit']) ? (int)$this['limit'] : 0;
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