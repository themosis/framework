<?php

namespace Themosis\Forms\Contracts;

interface FormInterface
{
    /**
     * Output the form as HTML.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Set the form prefix. If fields are attached to the form,
     * all fields are updated with the given prefix.
     *
     * @param string $prefix
     *
     * @return FormInterface
     */
    public function setPrefix(string $prefix): FormInterface;

    /**
     * Return the form prefix.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Return the form repository instance.
     *
     * @return FormRepositoryInterface
     */
    public function repository(): FormRepositoryInterface;
}
