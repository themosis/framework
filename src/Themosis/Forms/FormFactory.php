<?php

namespace Themosis\Forms;

use Themosis\Html\HtmlBuilder;

class FormFactory
{
    public function __construct()
    {
    }

    /**
     * Creates a new form instance and returns it.
     */
    public function make()
    {
        return new Form(new HtmlBuilder());
    }
}
