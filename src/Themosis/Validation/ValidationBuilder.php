<?php
namespace Themosis\Validation;

class ValidationBuilder {

    public function __construct()
    {

    }

    /**
     * Runs a validation rule on a single passed data.
     *
     * @param mixed $data The given data: string, int, array, bool...
     * @param array $rules The rules to use for validation.
     * @return mixed
     */
    public function single($data, array $rules)
    {
        foreach($rules as $rule){

            // Set rule method.
            $signature = 'validate_'.$rule;

            // Check if the datas given is an array
            // If array, parse each item and return them
            // into the array.
            if(is_array($data)){

                // Overwrite each array value
                foreach($data as $key => $value){

                    // Validate the data value.
                    $data[$key] = $this->$signature($value);

                }

            } else {

                // The data is a string or single value.
                $data = $this->$signature($data);

            }

        }

        return $data;
    }

    /**
     * Validate a value with only alphabetic characters.
     *
     * @param string $data The data to validate.
     * @return string
     */
    private function validate_alpha($data)
    {
        return ctype_alpha($data) ? $data : '';
    }

    /**
     * Validate a value with only numeric characters.
     *
     * @param string $data The data to validate.
     * @return string
     */
    private function validate_num($data)
    {
        return ctype_digit($data) ? $data : '';
    }

    /**
     * Validate a text field value.
     *
     * @param string $data The data to validate.
     * @return string
     */
    private function validate_textfield($data)
    {
        return sanitize_text_field($data);
    }

    /**
     * Validate an email value.
     *
     * @param string $data The data to validate.
     * @return string
     */
    private function validate_email($data)
    {
        $email = sanitize_email($data);

        return is_email($email) ? $email : '';
    }

} 