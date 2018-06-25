<?php

namespace Themosis\Core\Forms;

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
        $factory = $this->getFormFactory();

        return $formClass->build($factory)->get();
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
}
