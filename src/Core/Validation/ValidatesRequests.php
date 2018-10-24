<?php

namespace Themosis\Core\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ValidatesRequests
{
    /**
     * Run the validation routine against the given validator.
     *
     * @param Validator|array $validator
     * @param Request|null    $request
     *
     * @throws ValidationException
     *
     * @return array
     */
    public function validateWith($validator, Request $request = null)
    {
        $request = $request ?: request();

        if (is_array($validator)) {
            $validator = $this->getValidationFactory()->make($request->all(), $validator);
        }

        /** @var \Illuminate\Validation\Validator $validator */
        $validator->validate();

        return $this->extractInputFromRules($request, $validator->getRules());
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param Request $request
     * @param array   $rules
     * @param array   $messages
     * @param array   $attributes
     *
     * @return array
     */
    public function validate(Request $request, array $rules, array $messages = [], array $attributes = [])
    {
        $this->getValidationFactory()
            ->make($request->all(), $rules, $messages, $attributes)
            ->validate();

        return $this->extractInputFromRules($request, $rules);
    }

    /**
     * @param $errorBag
     * @param Request $request
     * @param array   $rules
     * @param array   $messages
     * @param array   $attributes
     *
     * @throws ValidationException
     *
     * @return array
     */
    public function validateWithBag(
        $errorBag,
        Request $request,
        array $rules,
        array $messages = [],
        array $attributes = []
    ) {
        try {
            return $this->validate($request, $rules, $messages, $attributes);
        } catch (ValidationException $e) {
            $e->errorBag = $errorBag;
            throw $e;
        }
    }

    /**
     * Get the request input based on the given validation rules.
     *
     * @param Request $request
     * @param array   $rules
     *
     * @return array
     */
    protected function extractInputFromRules(Request $request, array $rules)
    {
        return $request->only(collect($rules)->keys()->map(function ($rule) {
            return Str::contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());
    }

    /**
     * Get the validation factory.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return app(Factory::class);
    }
}
