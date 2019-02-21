<?php

namespace Themosis\Field;

use Illuminate\Contracts\View\Factory as ViewFactoryInterface;
use Themosis\Core\Application;
use Themosis\Field\Contracts\FieldFactoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\Types\ButtonType;
use Themosis\Forms\Fields\Types\CheckboxType;
use Themosis\Forms\Fields\Types\ChoiceType;
use Themosis\Forms\Fields\Types\CollectionType;
use Themosis\Forms\Fields\Types\ColorType;
use Themosis\Forms\Fields\Types\EditorType;
use Themosis\Forms\Fields\Types\EmailType;
use Themosis\Forms\Fields\Types\HiddenType;
use Themosis\Forms\Fields\Types\IntegerType;
use Themosis\Forms\Fields\Types\MediaType;
use Themosis\Forms\Fields\Types\NumberType;
use Themosis\Forms\Fields\Types\PasswordType;
use Themosis\Forms\Fields\Types\SubmitType;
use Themosis\Forms\Fields\Types\TextareaType;
use Themosis\Forms\Fields\Types\TextType;

/**
 * Field factory.
 */
class Factory implements FieldFactoryInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ViewFactoryInterface
     */
    protected $viewFactory;

    public function __construct(Application $app, ViewFactoryInterface $factory)
    {
        $this->app = $app;
        $this->viewFactory = $factory;
    }

    /**
     * Return a text type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function text(string $name, array $options = []): FieldTypeInterface
    {
        return (new TextType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a password type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function password(string $name, array $options = []): FieldTypeInterface
    {
        return (new PasswordType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a number field type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function number(string $name, array $options = []): FieldTypeInterface
    {
        return (new NumberType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return an integer field type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function integer(string $name, array $options = []): FieldTypeInterface
    {
        return (new IntegerType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return an email type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function email(string $name, array $options = []): FieldTypeInterface
    {
        return (new EmailType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a DateField instance.
     *
     * @param string $name       The name attribute of the date input.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\DateField
     */
    public function date($name, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge(['class' => 'newtag'], $attributes, ['data-field' => 'date']),
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\DateField', $properties);
    }

    /**
     * Return a textarea type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function textarea(string $name, array $options = []): FieldTypeInterface
    {
        return (new TextareaType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a checkbox type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function checkbox(string $name, array $options = []): FieldTypeInterface
    {
        return (new CheckboxType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a choice type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function choice(string $name, array $options = []): FieldTypeInterface
    {
        return (new ChoiceType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a media type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function media($name, array $options = []): FieldTypeInterface
    {
        return (new MediaType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return an editor type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function editor($name, array $options = []): FieldTypeInterface
    {
        return (new EditorType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a collection type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function collection($name, array $options = []): FieldTypeInterface
    {
        return (new CollectionType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a color type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function color($name, array $options = []): FieldTypeInterface
    {
        return (new ColorType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a button type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function button(string $name, array $options = []): FieldTypeInterface
    {
        return (new ButtonType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a submit type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function submit(string $name, array $options = []): FieldTypeInterface
    {
        return (new SubmitType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }

    /**
     * Return a hidden type instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return FieldTypeInterface
     */
    public function hidden(string $name, array $options = []): FieldTypeInterface
    {
        return (new HiddenType($name))
            ->setLocale($this->app->getLocale())
            ->setViewFactory($this->viewFactory)
            ->setOptions($options);
    }
}
