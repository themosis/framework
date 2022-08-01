<?php

namespace Themosis\Tests\Forms;

use Illuminate\Config\Repository;
use League\Fractal\Manager;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Forms\Resources\Factory;
use Themosis\Forms\Resources\Transformers\FieldTransformer;
use Themosis\Tests\Forms\Resources\Transformers\CustomFieldTransformer;

class FieldsTest extends TestCase
{
    protected $application;

    protected function getApplication()
    {
        if (! is_null($this->application)) {
            return $this->application;
        }

        $this->application = new Application();

        $this->application->bind('config', function () {
            $config = new Repository();
            $config->set('app.locale', 'en_US');

            return $config;
        });

        return $this->application;
    }

    protected function getFieldsFactory()
    {
        $viewFactory = $this->getMockBuilder('Illuminate\View\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        return new \Themosis\Field\Factory($this->getApplication(), $viewFactory);
    }

    public function testResourceFactoryReturnFormTransformersInstances()
    {
        $factory = new Factory();

        $this->assertInstanceOf(
            FieldTransformer::class,
            $factory->make('FieldTransformer'),
        );
    }

    public function testResourceFactoryReturnCustomTransformerWithFQCN()
    {
        $factory = new Factory();

        $this->assertInstanceOf(
            CustomFieldTransformer::class,
            $factory->make('Themosis\\Tests\\Forms\\Resources\\Transformers\\CustomFieldTransformer'),
        );
    }

    public function testFieldsType()
    {
        $fields = $this->getFieldsFactory();

        $text = $fields->text('name');
        $textarea = $fields->textarea('message');
        $button = $fields->button('clickme');
        $checkbox = $fields->checkbox('activate');
        $choice = $fields->choice('chooseme');
        $email = $fields->email('email');
        $hidden = $fields->hidden('secret');
        $integer = $fields->integer('count');
        $number = $fields->number('surface');
        $password = $fields->password('hideme');
        $submit = $fields->submit('send');

        $this->assertEquals('text', $text->getType());
        $this->assertEquals('textarea', $textarea->getType());
        $this->assertEquals('button', $button->getType());
        $this->assertEquals('checkbox', $checkbox->getType());
        $this->assertEquals('choice', $choice->getType());
        $this->assertEquals('email', $email->getType());
        $this->assertEquals('hidden', $hidden->getType());
        $this->assertEquals('integer', $integer->getType());
        $this->assertEquals('number', $number->getType());
        $this->assertEquals('password', $password->getType());
        $this->assertEquals('submit', $submit->getType());
    }

    protected function expected(array $expected)
    {
        return array_merge([
            'attributes' => [],
            'basename' => '',
            'component' => null,
            'data_type' => '',
            'default' => '',
            'name' => '',
            'options' => [
                'group' => 'default',
                'info' => '',
                'l10n' => [],
            ],
            'label' => [],
            'theme' => '',
            'type' => 'input',
            'validation' => [
                'errors' => true,
                'messages' => [],
                'placeholder' => '',
                'rules' => '',
            ],
            'value' => '',
        ], $expected);
    }

    public function testFormFieldTextTypeToJSON()
    {
        $fields = $this->getFieldsFactory();

        $name = $fields->text('name')
            ->setManager(new Manager())
            ->setResourceTransformerFactory(new Factory());

        $expected = $this->expected([
            'attributes' => [
                'id' => 'th_name_field',
            ],
            'basename' => 'name',
            'component' => 'themosis.fields.text',
            'name' => 'th_name',
            'label' => [
                'inner' => 'Name',
                'attributes' => [
                    'for' => 'th_name_field',
                ],
            ],
            'type' => 'text',
            'validation' => [
                'errors' => true,
                'messages' => [],
                'placeholder' => 'name',
                'rules' => '',
            ],
        ]);

        $this->assertEquals($expected, $name->toArray());
        $this->assertEquals(json_encode($expected), $name->toJson());
    }

    public function testFormFieldTextareaTypeToJSON()
    {
        $fields = $this->getFieldsFactory();

        $message = $fields->textarea('message')
            ->setManager(new Manager())
            ->setResourceTransformerFactory(new Factory());

        $expected = $this->expected([
            'attributes' => [
                'id' => 'th_message_field',
            ],
            'basename' => 'message',
            'component' => 'themosis.fields.textarea',
            'name' => 'th_message',
            'label' => [
                'inner' => 'Message',
                'attributes' => [
                    'for' => 'th_message_field',
                ],
            ],
            'type' => 'textarea',
            'validation' => [
                'errors' => true,
                'messages' => [],
                'placeholder' => 'message',
                'rules' => '',
            ],
        ]);

        $this->assertEquals($expected, $message->toArray());
        $this->assertEquals(json_encode($expected), $message->toJson());
    }

    public function testFormFieldChoiceTypeToJSON()
    {
        $fields = $this->getFieldsFactory();

        $colors = $fields->choice('colors', [
            'choices' => [
                'red',
                'green',
                'blue',
            ],
        ])
            ->setManager(new Manager())
            ->setResourceTransformerFactory(new Factory());

        $expected = $this->expected([
            'attributes' => [
                'id' => 'th_colors_field',
            ],
            'basename' => 'colors',
            'component' => 'themosis.fields.choice',
            'name' => 'th_colors',
            'label' => [
                'inner' => 'Colors',
                'attributes' => [
                    'for' => 'th_colors_field',
                ],
            ],
            'options' => [
                'group' => 'default',
                'info' => '',
                'l10n' => [],
                'expanded' => false,
                'multiple' => false,
                'choices' => [
                    ['key' => 'Red', 'value' => 'red', 'type' => 'option'],
                    ['key' => 'Green', 'value' => 'green', 'type' => 'option'],
                    ['key' => 'Blue', 'value' => 'blue', 'type' => 'option'],
                ],
                'layout' => 'select',
            ],
            'type' => 'choice',
            'validation' => [
                'errors' => true,
                'messages' => [],
                'placeholder' => 'colors',
                'rules' => '',
            ],
            'value' => null,
        ]);

        $this->assertEquals($expected, $colors->toArray());
        $this->assertEquals(json_encode($expected), $colors->toJson());
    }

    public function testFieldsHaveComponentsNames()
    {
        $fields = $this->getFieldsFactory();

        $text = $fields->text('name');
        $email = $fields->email('email');

        $this->assertEquals('themosis.fields.text', $text->getComponent());
        $this->assertEquals('themosis.fields.email', $email->getComponent());
    }

    public function testNumberFieldOptions()
    {
        $fields = $this->getFieldsFactory();

        $price = $fields->number('price');

        $this->assertEquals(0, $price->getOption('precision'));

        $price = $fields->number('other', [
            'precision' => 4,
        ]);

        $this->assertEquals(4, $price->getOption('precision'));
    }

    public function testFieldCanBeShownInRest()
    {
        $fields = $this->getFieldsFactory();

        $name = $fields->text('name');

        $this->assertFalse($name->getOption('show_in_rest'));

        $email = $fields->email('email', [
            'show_in_rest' => true,
        ]);

        $this->assertTrue($email->getOption('show_in_rest'));
    }
}
