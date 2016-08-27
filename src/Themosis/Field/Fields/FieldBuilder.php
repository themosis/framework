<?php

namespace Themosis\Field\Fields;

use Illuminate\View\Factory;
use Themosis\Foundation\DataContainer;

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
     * A view instance.
     *
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * FieldBuilder instance.
     *
     * @param array                    $properties Field instance properties.
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(array $properties, Factory $view)
    {
        $this->properties = $properties;
        $this->view = $view;
        $this['features'] = $this->parseFeatures();
        $this['atts'] = $this->parseAttributes();
    }

    /**
     * Method to override in the child class to define
     * its input type property.
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
        if (isset($atts['name'])) {
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
     * If no type is defined, default to an array containing the 'image' mime type only.
     *
     * Need to "serialize" the array for output as a string value. We can use a separated list with commas.
     */
    protected function setType()
    {
        // Retrieve allowed WordPress mime types.
        // If none are defined, this will be the default types.
        // Users will be able to add any media file.
        $allowed = $this->getAllowedMimeTypes();

        $features = $this['features'];

        // User has defined media type(s)
        // It might be a string or an array.
        if (isset($features['type'])) {
            $type = $features['type'];

            // Isset... Check if is a string... If it is, turn it into an array.
            if (is_string($type)) {
                $type = [$type];
            }

            // $type is an array, let's check its values.
            $type = array_intersect($type, $allowed);

            if (!empty($type)) {
                $features['type'] = $type;
            } else {
                $features['type'] = $allowed;
            }
        } else {
            $features['type'] = $allowed;
        }

        // "Serialize" the $features['type'] value. Build a comma separated list of types.
        $features['type'] = implode(',', $features['type']);

        // Set the features back.
        $this['features'] = $features;

        // Set the data-type attribute.
        $atts = $this['atts'];
        $atts['data-type'] = $this['features']['type'];
        $this['atts'] = $atts;
    }

    /**
     * Return a simplified list of allowed mime types.
     * This will automatically authorize user defined mime types.
     *
     * @return array
     */
    protected function getAllowedMimeTypes()
    {
        $types = get_allowed_mime_types();

        $allowed = array_map(function ($mime) {
            // type/ext
            $explodedMime = explode('/', $mime);

            return array_shift($explodedMime);
        }, $types);

        return array_values(array_unique($allowed));
    }

    /**
     * Define the limit of media files or rows we can add.
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
     */
    protected function defaultCheckableValue()
    {
        if ('0' === $this['value']) {
            return;
        }

        if (empty($this['value'])) {
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
