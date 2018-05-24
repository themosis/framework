<?php

namespace Themosis\Forms\Contracts;

interface FormFactoryInterface
{
    /**
     * Create a FormBuilderInterface instance.
     *
     * @param mixed $data Data object (DTO).
     *
     * @return $this
     */
    public function make($data = null);
}
