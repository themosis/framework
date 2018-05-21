<?php

namespace Themosis\Forms\Fields\Types;

class CheckboxType extends BaseType
{
    /**
     * A list of checkbox options (values).
     *
     * @var array
     */
    protected $options;

    /**
     * A text label for the field.
     *
     * @var string
     */
    protected $label;

    /**
     * Allow to define multiple checkboxes.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set the label string.
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    protected function build()
    {
        if (! is_null($this->options) && is_array($this->options)) {
            return $this->checkableFields();
        }

        // Handle default value if one defined.
        if (! is_null($this->default) && is_string($this->default)) {
            $this['value'] = $this->default;
        }

        if (! is_null($this->label)) {
            // @todo Handle prefixing of "id" attributes ?
            $this['id'] = 'f_'.$this['value'];

            return '<input type="checkbox"'.$this->attributes().'><label for="'.$this['id'].'">'.$this->label.'</label>';
        }

        return '<input type="checkbox"'.$this->attributes().'>';
    }

    /**
     * Build sub-fields if multiple checkboxes options defined.
     *
     * @return string
     */
    protected function checkableFields()
    {
        $checkboxes = [];

        foreach ($this->options as $key => $label) {
            $field = new self($this->html);
            $field->setAttributes($this->getAttributes());

            if (is_numeric($key)) {
                $field['value'] = $label;
            } else {
                $field['value'] = $key;
                $field->setLabel($label);
            }

            $checkboxes[] = $field->build();
        }

        return implode('', $checkboxes);
    }
}
