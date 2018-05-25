<?php

namespace Themosis\Forms\Contracts;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Request;

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

    /**
     * Handle request in order to validate form data.
     *
     * @param Request $request
     *
     * @return FormInterface
     */
    public function handleRequest(Request $request): FormInterface;

    /**
     * Check if submitted form is valid or not.
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Return a list of errors.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag;

    /**
     * Return error messages for a specific field.
     * By setting the second parameter to true, a user
     * can fetch the first error message only on the
     * mentioned field.
     *
     * @param string $name
     * @param bool   $first
     *
     * @return mixed
     */
    public function error(string $name, bool $first = false);
}
