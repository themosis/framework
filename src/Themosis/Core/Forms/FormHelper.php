<?php

namespace Themosis\Core\Forms;

use Themosis\Field\Contracts\FieldFactoryInterface;
use Themosis\Forms\Contracts\FormFactoryInterface;
use Themosis\Forms\Contracts\Formidable;
use Themosis\Forms\Contracts\FormInterface;

trait FormHelper
{
    /**
     * Create and return a form instance.
     *
     * @param Formidable $formClass
     *
     * @return FormInterface
     */
    public function form(Formidable $formClass)
    {
        return $formClass->build($this->getFormFactory(), $this->getFieldsFactory());
    }

    /**
     * Retrieve the form factory instance.
     *
     * @return FormFactoryInterface
     */
    private function getFormFactory()
    {
        return app('form');
    }

    /**
     * Retrieve the fields factory instance.
     *
     * @return FieldFactoryInterface
     */
    private function getFieldsFactory()
    {
        return app('field');
    }
}
