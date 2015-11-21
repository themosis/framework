<?php
namespace Themosis\Field\Fields;

use Themosis\Core\DataContainer;
use Themosis\View\ViewFactory;

abstract class FieldBuilder extends DataContainer
{
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
     * A view instance
     * @var ViewFactory
     */
    protected $view;

    /**
     * FieldBuilder instance
     *
     * @param array $properties Field instance properties.
     * @param ViewFactory $view
     */
    public function __construct(array $properties, ViewFactory $view)
    {
        $this->properties = $properties;
        $this->view = $view;
        $this['features'] = $this->parseFeatures();
        $this['atts'] = $this->parseAttributes();
    }

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
     * Parse and prepare field feature properties.
     *
     * @return array
     */
    protected function parseFeatures()
    {
        $f = $this['features'];

        // Check the title extra property.
        $f['title'] = isset($f['title']) ? ucfirst($f['title']) : ucfirst($this['name']);

        return $f;
    }

    /**
     * Parse and prepare the field tag attributes.
     *
     * @return array The parsed attributes.
     */
    protected function parseAttributes()
    {
        $atts = $this['atts'];

        // Check if developer has defined a custom name attribute.
        // If so, remove it.
        if (isset($atts['name']))
        {
            unset($atts['name']);
        }

        // Set the 'id' attribute.
        $atts['id'] = isset($atts['id']) ? $atts['id'] : $this['name'].'-id';

        // Set the 'class' attribute.
        $atts['class'] = isset($atts['class']) ? $atts['class'] : 'field-'.$this['name'];

        return $atts;
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
        $features = $this['features'];

        if (isset($features['type']) && !in_array($features['type'], $allowed))
        {
            $features['type'] = 'image';
        }
        elseif (!isset($features['type']))
        {
            $features['type'] = 'image';
        }

        // Set the features back.
        $this['features'] = $features;

        // Set the data-type attribute.
        $atts = $this['atts'];
        $atts['data-type'] = $this['features']['type'];
        $this['atts'] = $atts;
    }

    /**
     * Define the limit of media files or rows we can add.
     *
     * @return void
     */
    protected function setLimit()
    {
        $features = $this['features'];
        $limit = isset($features['limit']) ? (int) $features['limit'] : 0;
        $features['limit'] = $limit;
        $this['features'] = $features;
    }

    /**
     * Define a default value as array for checkable fields.
     *
     * @return void
     */
    protected function defaultCheckableValue()
    {
        if ('0' === $this['value']) return;

        if (empty($this['value']))
        {
            $this['value'] = [];
        }
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

} 