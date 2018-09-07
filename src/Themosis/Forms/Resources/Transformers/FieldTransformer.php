<?php

namespace Themosis\Forms\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\ChoiceList\ChoiceListInterface;

class FieldTransformer extends TransformerAbstract
{
    /**
     * Transform single field.
     *
     * @param FieldTypeInterface $field
     *
     * @return array
     */
    public function transform(FieldTypeInterface $field)
    {
        $default = [
            'attributes' => $field->getAttributes(),
            'basename' => $field->getBaseName(),
            'component' => $field->getComponent(),
            'data_type' => $field->getOption('data_type', ''),
            'default' => $field->getOption('data', ''),
            'name' => $field->getName(),
            'options' => [
                'group' => $field->getOption('group', 'default'),
                'info' => $field->getOption('info', '')
            ],
            'label' => [
                'inner' => $field->getOption('label'),
                'attributes' => $field->getOption('label_attr', [])
            ],
            'theme' => $field->getTheme(),
            'type' => $field->getType(),
            'validation' => [
                'errors' => $field->getOption('errors', true),
                'messages' => $field->errors()->toArray(),
                'placeholder' => $field->getOption('placeholder'),
                'rules' => $field->getOption('rules', '')
            ],
            'value' => $field->getValue(),
        ];

        return $this->with($field, function (FieldTypeInterface $field) use ($default) {
            /**
             * Handle choice type field props.
             */
            if ('choice' === $field->getType()) {
                $choices = $field->getOption('choices', []);

                if ($choices instanceof ChoiceListInterface) {
                    return array_merge($default, [
                        'choices' => $choices->format()->get()
                    ]);
                }
            }

            return $default;
        });
    }

    /**
     * Attach properties to transformed output.
     *
     * @param FieldTypeInterface $field
     * @param \Closure           $callback
     *
     * @return array
     */
    protected function with(FieldTypeInterface $field, \Closure $callback): array
    {
        return $callback($field);
    }
}
