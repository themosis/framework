<?php

namespace Themosis\Forms;

use Themosis\Html\HtmlBuilder;

abstract class Form
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

    public function __construct(HtmlBuilder $html)
    {
        $this->html = $html;

        // Set default form attributes.
        $this->setAttributes([
            'method' => 'post'
        ]);
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
}
