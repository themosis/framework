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
        $this->fields[] = $field;

        return $this;
    }
}
