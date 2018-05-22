<?php

namespace Themosis\Forms;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Fields\FieldBuilder;

/**
 * Class Form
 *
 * @package Themosis\Forms
 */
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
     * Form groups.
     *
     * @var array
     */
    protected $groups = [
        'default'
    ];

    /**
     * Fields organized by group.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * All fields.
     *
     * @var FieldTypeInterface[]
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
    public function render(): string
    {
        return '';
    }

    /**
     * Add a field to the form instance.
     *
     * @param FieldTypeInterface $field
     *
     * @return FormInterface
     */
    public function addField(FieldTypeInterface $field): FormInterface
    {
        // We store all fields together
        // as well as per group. On each form,
        // there is a "default" group defined where
        // all fields are attached to. A user can specify
        // a form group to the passed options on the "add"
        // method of the FormBuilder instance.
        $this->allFields[$field->getBaseName()] = $field;
        $this->fields['default'][$field->getBaseName()] = $field;

        return $this;
    }

    /**
     * Set the form prefix. If fields are attached to the form,
     * all fields are updated with the given prefix.
     *
     * @param string $prefix
     *
     * @return FormInterface
     */
    public function setPrefix(string $prefix): FormInterface
    {
        $this->prefix = $prefix;

        // Update all attached fields with the given prefix.
        foreach ($this->allFields as $field) {
            $field->setPrefix($prefix);
        }

        return $this;
    }

    /**
     * Return the form prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Return a list of attached fields instances.
     *
     * @param string $name
     * @param string $group
     *
     * @return mixed A FieldTypeInterface instance or an array of fields.
     */
    public function getFields(string $name = '', string $group = 'default')
    {
        return $this->fields[$group][$name] ?? $this->fields[$group];
    }
}
