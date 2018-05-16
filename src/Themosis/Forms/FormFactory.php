<?php

namespace Themosis\Forms;

use Themosis\Forms\Fields\FieldBuilder;
use Themosis\Html\HtmlBuilder;

class FormFactory
{
    /**
     * FormFactory constructor.
     */
    public function __construct()
    {
    }

    /**
     * Creates a new form instance and returns it.
     */
    public function make()
    {
        return new Form();
    }
}
