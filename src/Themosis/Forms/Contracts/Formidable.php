<?php

namespace Themosis\Forms\Contracts;

interface Formidable
{
    /**
     * Build and configure a re-usable form.
     *
     * @param $factory FormFactoryInterface
     *
     * @return Formidable
     */
    public function build(FormFactoryInterface $factory): Formidable;

    /**
     * Get the generated form instance.
     *
     * @return FormInterface
     */
    public function get(): FormInterface;
}
