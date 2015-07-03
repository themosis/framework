<?php
namespace Themosis\Field\Fields;

use Themosis\View\ViewFactory;

class CollectionField extends FieldBuilder implements IField
{
    /**
     * Define a collection field instance.
     *
     * @param array $properties
     * @param ViewFactory $view
     */
    public function __construct(array $properties, ViewFactory $view)
    {
        parent::__construct($properties, $view);
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
    protected function setType()
    {
        $allowed = ['image', 'application', 'video', 'audio'];

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
    protected function setLimit()
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
        return $this->view->make('metabox._themosisCollectionField', ['field' => $this])->render();
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