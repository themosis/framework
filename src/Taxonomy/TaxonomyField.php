<?php

namespace Themosis\Taxonomy;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\Contracts\CanHandleTerms;
use Themosis\Forms\Fields\Types\BaseType;
use Themosis\Hook\IHook;
use Themosis\Taxonomy\Contracts\TaxonomyInterface;

class TaxonomyField
{
    /**
     * @var TaxonomyFieldRepository
     */
    protected $repository;

    /**
     * @var \Illuminate\View\Factory
     */
    protected $factory;

    /**
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $validator;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var TaxonomyInterface
     */
    protected $taxonomy;

    /**
     * @var array
     */
    protected $options;

    public function __construct(
        TaxonomyInterface $taxonomy,
        TaxonomyFieldRepository $repository,
        \Illuminate\View\Factory $factory,
        \Illuminate\Contracts\Validation\Factory $validator,
        IHook $action,
        array $options
    ) {
        $this->taxonomy = $taxonomy;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->validator = $validator;
        $this->action = $action;
        $this->options = $options;
    }

    /**
     * Add a taxonomy custom field.
     *
     * @param FieldTypeInterface $field
     *
     * @return $this
     */
    public function add(FieldTypeInterface $field): TaxonomyField
    {
        $field->setTheme($this->options['theme'] ?? 'themosis.taxonomy');
        $field->setPrefix($this->options['prefix'] ?? 'th_');

        $this->repository->add($field);

        return $this;
    }

    /**
     * Set taxonomy custom fields.
     */
    public function set()
    {
        // Register term meta on Rest API.
        if (function_exists('current_filter') && 'init' === current_filter()) {
            call_user_func($this->register());
        } else {
            $this->action->add('init', $this->register());
        }

        // Display fields.
        $this->action->add($this->taxonomy->getName().'_add_form_fields', $this->outputAddFields());
        $this->action->add($this->taxonomy->getName().'_edit_form_fields', $this->outputEditFields());

        // Save fields values.
        $this->action->add([
            'create_'.$this->taxonomy->getName(),
            'edit_'.$this->taxonomy->getName()
        ], $this->save());
    }

    /**
     * Return the callback used to register term meta.
     *
     * @return \Closure
     */
    protected function register()
    {
        return function () {
            foreach ($this->repository as $field) {
                register_meta('term', $field->getName(), [
                    'type' => $field->getOption('data_type', 'string'),
                    'single' => ! $field->getOption('multiple', false),
                    'show_in_rest' => $field->getOption('show_in_rest', false),
                    'sanitize_callback' => $this->sanitize($field)
                ]);
            }
        };
    }

    /**
     * Sanitize term meta value.
     *
     * @param FieldTypeInterface $field
     *
     * @return \Closure
     */
    protected function sanitize(FieldTypeInterface $field)
    {
        return function ($value, $key, $type) {
            $validator = $this->validator->make(
                [$key => $value],
                $this->getTermRules(),
                $this->getTermMessages(),
                $this->getTermPlaceholders()
            );

            $validation = $validator->valid();

            return $validation[$key] ?? null;
        };
    }

    /**
     * Return the function managing term meta registration.
     *
     * @return \Closure
     */
    protected function save()
    {
        return function ($term_id) {
            /** @var Validator $validator */
            $validator = $this->validator->make(
                $this->getTermData(app('request')),
                $this->getTermRules(),
                $this->getTermMessages(),
                $this->getTermPlaceholders()
            );

            $validation = $validator->valid();

            foreach ($this->repository->all() as $field) {
                /** @var FieldTypeInterface|BaseType|CanHandleTerms $field */
                $field->setErrorMessageBag($validator->errors());

                if (method_exists($field, 'termSave')) {
                    $field->termSave($validation[$field->getName()] ?? null, $term_id);
                } else {
                    throw new TaxonomyException(
                        'Unable to save ['.$field->getName().']. The [termSave] method is missing.'
                    );
                }
            }
        };
    }

    /**
     * Fetch raw data from the request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getTermData(Request $request)
    {
        $data = [];

        foreach ($this->repository as $field) {
            $data[$field->getName()] = $request->get($field->getName());
        }

        return $data;
    }

    /**
     * Return terms rules.
     *
     * @return array
     */
    protected function getTermRules()
    {
        $rules = [];

        foreach ($this->repository as $field) {
            $rules[$field->getName()] = $field->getOption('rules');
        }

        return $rules;
    }

    /**
     * Return terms errors messages.
     *
     * @return array
     */
    protected function getTermMessages()
    {
        $messages = [];

        foreach ($this->repository as $field) {
            foreach ($field->getOption('messages') as $rule => $message) {
                $messages[$field->getName().'.'.$rule] = $message;
            }
        }

        return $messages;
    }

    /**
     * Return terms placeholders.
     *
     * @return array
     */
    protected function getTermPlaceholders()
    {
        $placeholders = [];

        foreach ($this->repository as $field) {
            $placeholders[$field->getName()] = $field->getOption('placeholder');
        }

        return $placeholders;
    }

    /**
     * Handle display of fields on "add" term screen.
     *
     * @return \Closure
     */
    protected function outputAddFields()
    {
        return function () {
            echo $this->factory->make('themosis.taxonomy.add', [
                'fields' => $this->repository
            ])->render();
        };
    }

    /**
     * Handle display of fields on "edit" term screen.
     *
     * @return \Closure
     */
    protected function outputEditFields()
    {
        return function ($term) {
            /** @var \WP_Term $term */
            foreach ($this->repository as $field) {
                if (method_exists($field, 'termGet')) {
                    $field->termGet($term->term_id);
                }
            }

            echo $this->factory->make('themosis.taxonomy.edit', [
                'fields' => $this->repository
            ])->render();
        };
    }
}
