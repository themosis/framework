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
     * Method that return the field input type.
     *
     * @return string
     */
    public function getFieldType()
    {
        return $this->type;
    }

} 