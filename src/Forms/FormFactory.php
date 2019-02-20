<?php

namespace Themosis\Forms;

use Illuminate\Contracts\Validation\Factory as ValidationFactoryInterface;
use Illuminate\Contracts\View\Factory as ViewFactoryInterface;
use League\Fractal\Manager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Themosis\Forms\Contracts\FormBuilderInterface;
use Themosis\Forms\Contracts\FormFactoryInterface;
use Themosis\Forms\DataMappers\DataMapperManager;
use Themosis\Forms\Fields\FieldsRepository;
use Themosis\Forms\Resources\Factory;

class FormFactory implements FormFactoryInterface
{
    /**
     * @var ValidationFactoryInterface
     */
    protected $validation;

    /**
     * @var ViewFactoryInterface
     */
    protected $viewer;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Form generator/builder.
     *
     * @var FormBuilderInterface
     */
    protected $builder;

    /**
     * Form instances default attributes.
     *
     * @var array
     */
    protected $attributes = [
        'method' => 'post'
    ];

    /**
     * Create a FormBuilderInterface instance.
     *
     * @param mixed  $dataClass Data object (DTO).
     * @param array  $options
     * @param string $builder   A FieldBuilderInterface class.
     *
     * @return FormBuilderInterface
     */
    public function make($dataClass = null, $options = [], $builder = FormBuilder::class): FormBuilderInterface
    {
        $dataMapperManager = new DataMapperManager(PropertyAccess::createPropertyAccessor());

        $form = new Form(
            $dataClass,
            new FieldsRepository(),
            $this->validation,
            $this->viewer,
            $dataMapperManager
        );
        $form->setManager($this->manager);
        $form->setResourceTransformerFactory($this->factory);
        $form->setAttributes($this->attributes);
        $form->setOptions($options);

        $this->builder = new $builder($form, $dataMapperManager, $dataClass);

        return $this->builder;
    }

    public function __construct(
        ValidationFactoryInterface $validation,
        ViewFactoryInterface $viewer,
        Manager $manager,
        Factory $factory
    ) {
        $this->validation = $validation;
        $this->viewer = $viewer;
        $this->manager = $manager;
        $this->factory = $factory;
    }
}
