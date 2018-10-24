<?php

namespace Themosis\Field\Contracts;

use Themosis\Forms\Contracts\FieldTypeInterface;

interface FieldFactoryInterface
{
    /**
     * Return a text field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function text(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a password field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function password(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a number field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function number(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return an integer field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function integer(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return an email field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function email(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a textarea field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function textarea(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a checkbox field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function checkbox(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a choice field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function choice(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a button field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function button(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a submit field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function submit(string $name, array $options = []): FieldTypeInterface;

    /**
     * Return a hidden field instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function hidden(string $name, array $options = []): FieldTypeInterface;
}
