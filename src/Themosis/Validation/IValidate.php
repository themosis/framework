<?php

namespace Themosis\Validation;

interface IValidate
{
    /**
     * Runs a validation rule on a single passed data.
     *
     * @param mixed $data  The given data: string, int, array, bool...
     * @param array $rules The rules to use for validation.
     *
     * @return mixed
     */
    public function single($data, array $rules);

    /**
     * Validate multiple inputs.
     *
     * @param array $data  The inputs to validate.
     * @param array $rules The rules in order to validate.
     *
     * @return array
     */
    public function multiple(array $data, array $rules);

    /**
     * Check if a given array is associative.
     *
     * @param array $arr
     *
     * @return bool True if associative.
     */
    public function isAssociative(array $arr);
}
