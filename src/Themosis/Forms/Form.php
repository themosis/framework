<?php

namespace Themosis\Forms;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Fields\FieldBuilder;
use Themosis\Html\HtmlBuilder;

class Form implements FormInterface
{
    /**
     * Opening form tag attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var \Themosis\Html\HtmlBuilder
     */
    protected $html;

    /**
     * @var FieldBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $prefix = 'th_';

    /**
     * @var array
     */
    protected $groups = [
        'default'
    ];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * Fields organized by group.
     *
     * @var array
     */
    protected $allFields = [];

    public function __construct()
    {
    }

    /**
     * Set form open tag attributes.
     *
     * @param array $attributes
     *
     * @return \Themosis\Forms\Form;
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Render a form and returns its HTML structure.
     *
     * @return string
     */
    public function render()
    {
        return '<form '.$this->html->attributes($this->attributes).'></form>';
    }

    /**
     * Add a field to the form instance.
     * By default, each new field instance is added
     * to the "default" form group.
     *
     * @param string             $name
     * @param FieldTypeInterface $field
     * @param string             $group
     *
     * @return $this
     */
    protected function add($name, $field, $group = 'default')
    {
        $fieldInstance = new $field($this->html);

        // Set the "name" attribute.
        $fieldInstance['name'] = $name;

        $this->allFields[$group][$name] = $fieldInstance;

        return $this;
    }
}
