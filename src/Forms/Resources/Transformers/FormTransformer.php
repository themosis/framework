<?php

namespace Themosis\Forms\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;

class FormTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [
        'fields',
        'groups'
    ];

    /**
     * Transform single form.
     *
     * @param FieldTypeInterface $form
     *
     * @return array
     */
    public function transform(FieldTypeInterface $form)
    {
        return [
            'attributes' => $form->getAttributes(),
            'flush' => $form->getOption('flush', true),
            'locale' => $form->getLocale(),
            'nonce' => $form->getOption('nonce', '_themosisnonce'),
            'referer' => $form->getOption('referer', true),
            'tags' => $form->getOption('tags', true),
            'theme' => $form->getOption('theme', 'themosis'),
            'type' => $form->getType(),
            'validation' => [
                'errors' => $form->getOption('errors', true),
                'isValid' => $form->isValid()
            ]
        ];
    }

    /**
     * Include "fields" property to resource.
     *
     * @param FieldTypeInterface $form
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFields(FieldTypeInterface $form)
    {
        /** @var FieldTypeInterface|FormInterface $form */
        return $this->collection(
            $form->repository()->all(),
            $form->getResourceTransformerFactory()->make('FieldTransformer')
        );
    }

    /**
     * Include "groups" property to resource.
     *
     * @param FieldTypeInterface $form
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeGroups(FieldTypeInterface $form)
    {
        /** @var FieldTypeInterface|FormInterface $form */
        return $this->collection(
            $form->repository()->getGroups(),
            $form->getResourceTransformerFactory()->make('GroupTransformer')
        );
    }
}
