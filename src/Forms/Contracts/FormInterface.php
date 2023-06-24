<?php

namespace Themosis\Forms\Contracts;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use League\Fractal\Manager;

interface FormInterface
{
    /**
     * Return the form repository instance.
     */
    public function repository(): FieldsRepositoryInterface;

    /**
     * Handle request in order to validate form data.
     */
    public function handleRequest(Request $request): FormInterface;

    /**
     * Return a list of errors.
     */
    public function errors(): MessageBag;

    /**
     * Return error messages for a specific field.
     * By setting the second parameter to true, a user
     * can fetch the first error message only on the
     * mentioned field.
     *
     *
     * @return mixed
     */
    public function error(string $name, bool $first = false);

    /**
     * Set form group view file.
     */
    public function setGroupView(string $view, string $group = 'default'): FormInterface;

    /**
     * Get the view factory instance.
     */
    public function getViewer(): Factory;

    /**
     * Get the Fractal manager.
     */
    public function getManager(): Manager;
}
