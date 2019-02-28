<?php

namespace Themosis\Metabox;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Fields\Types\BaseType;
use Themosis\Metabox\Contracts\MetaboxInterface;
use Themosis\Metabox\Contracts\MetaboxManagerInterface;

class Manager implements MetaboxManagerInterface
{
    /**
     * @var ValidationFactory
     */
    protected $factory;

    public function __construct(ValidationFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Handle metabox initialization.
     * Set the metabox fields value and return the metabox instance.
     *
     * @param MetaboxInterface $metabox
     * @param Request          $request
     *
     * @return MetaboxInterface
     */
    public function getFields(MetaboxInterface $metabox, Request $request): MetaboxInterface
    {
        foreach ($metabox->repository()->all() as $field) {
            if (method_exists($field, 'metaboxGet')) {
                $field->metaboxGet($request->query('post_id'));
            }
        }

        return $metabox;
    }

    /**
     * Handle metabox post meta save.
     *
     * @param MetaboxInterface $metabox
     * @param Request          $request
     *
     * @throws MetaboxException
     *
     * @return MetaboxInterface
     */
    public function saveFields(MetaboxInterface $metabox, Request $request): MetaboxInterface
    {
        $post_id = $request->query('post_id');
        $data = $this->getMetaboxData(collect($request->get('fields')));
        $fields = $metabox->repository()->all();

        /** @var $validator Validator */
        $validator = $this->factory->make(
            $data,
            $this->getMetaboxRules($fields),
            $this->getMetaboxMessages($fields),
            $this->getMetaboxPlaceholders($fields)
        );

        $validatedData = $validator->valid();

        foreach ($fields as $field) {
            /** @var FieldTypeInterface|CanHandleMetabox|BaseType $field */
            $field->setErrorMessageBag($validator->errors());

            if (method_exists($field, 'metaboxSave')) {
                $value = isset($validatedData[$field->getName()]) ? $validatedData[$field->getName()] : null;
                $field->metaboxSave($value, $post_id);
            } else {
                throw new MetaboxException(
                    'Unable to save ['.$field->getName().']. The [metaboxSave] method is missing.'
                );
            }
        }

        return $metabox;
    }

    /**
     * Return the metabox data for validation.
     *
     * @param Collection $fields
     *
     * @return array
     */
    protected function getMetaboxData(Collection $fields)
    {
        $data = [];

        foreach ($fields as $field) {
            $data[$field['name']] = $field['value'];
        }

        return $data;
    }

    /**
     * Return the metabox rules for validation.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getMetaboxRules(array $fields)
    {
        $rules = [];

        foreach ($fields as $field) {
            /** @var FieldTypeInterface $field */
            $rules[$field->getName()] = $field->getOption('rules');
        }

        return $rules;
    }

    /**
     * Return the metabox validation messages.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getMetaboxMessages(array $fields)
    {
        // Each message is defined by field and its own rules.
        // In our case, we need to prepend the field name (attribute)
        // using a "dot" notation. Ex.: email.required
        $messages = [];

        foreach ($fields as $field) {
            /** @var FieldTypeInterface $field */
            foreach ($field->getOption('messages') as $attr => $message) {
                $messages[$field->getName().'.'.$attr] = $message;
            }
        }

        return $messages;
    }

    /**
     * Return the metabox messages placeholders.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getMetaboxPlaceholders(array $fields)
    {
        $placeholders = [];

        foreach ($fields as $field) {
            /** @var FieldTypeInterface $field */
            $placeholders[$field->getName()] = $field->getOption('placeholder');
        }

        return $placeholders;
    }
}
