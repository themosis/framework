<?php

namespace Themosis\Forms\Contracts;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;

interface FormInterface
{
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

    /**
     * Set form group view file.
     *
     * @param string $view
     * @param string $group
     *
     * @return FormInterface
     */
    public function setGroupView(string $view, string $group = 'default'): FormInterface;

    /**
     * Get the view factory instance.
     *
     * @return Factory
     */
    public function getViewer(): Factory;

    /**
     * Set form locale.
     *
     * @param string $locale
     *
     * @return FormInterface
     */
    public function setLocale(string $locale): FormInterface;

    /**
     * Return form locale.
     *
     * @return string
     */
    public function getLocale(): string;
}
