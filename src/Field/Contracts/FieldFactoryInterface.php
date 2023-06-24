<?php

namespace Themosis\Field\Contracts;

use Themosis\Forms\Contracts\FieldTypeInterface;

interface FieldFactoryInterface
{
    /**
     * Return a text field instance.
     */
    public function text(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a password field instance.
     */
    public function password(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a number field instance.
     */
    public function number(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return an integer field instance.
     */
    public function integer(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return an email field instance.
     */
    public function email(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a textarea field instance.
     */
    public function textarea(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a checkbox field instance.
     */
    public function checkbox(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a choice field instance.
     */
    public function choice(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a button field instance.
     */
    public function button(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a submit field instance.
     */
    public function submit(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a hidden field instance.
     */
    public function hidden(string $name, array $options = []): FieldTypeInterface;
}
