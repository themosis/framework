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
        if (!is_null($this->application)) {
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
        return new \Themosis\Field\Factory($this->getApplication());
    }

    public function testResourceFactoryReturnFormTransformersInstances()
    {
        $factory = new Factory();

        $this->assertInstanceOf(
            FieldTransformer::class,
            $factory->make('FieldTransformer')
        );
    }

    public function testResourceFactoryReturnCustomTransformerWithFQCN()
    {
        $factory = new Factory();

        $this->assertInstanceOf(
            CustomFieldTransformer::class,
            $factory->make('Themosis\\Tests\\Forms\\Resources\\Transformers\\CustomFieldTransformer')
        );
    }

    public function testFormFieldTextTypeToJSON()
    {
        $fields = $this->getFieldsFactory();

        $name = $fields->text('name')
            ->setManager(new Manager())
            ->setResourceTransformerFactory(new \Themosis\Forms\Resources\Factory());

        $expected = [
            'attributes' => [
                'id' => 'th_name_field'
            ],
            'basename' => 'name',
            'data_type' => '',
            'default' => '',
            'name' => 'th_name',
            'options' => [
                'group' => 'default',
                'info' => ''
            ],
            'label' => [
                'inner' => 'Name',
                'attributes' => [
                    'for' => 'th_name_field'
                ]
            ],
            'validation' => [
                'errors' => true,
                'messages' => [],
                'placeholder' => 'name',
                'rules' => ''
            ],
            'value' => null
        ];

        $this->assertEquals($expected, $name->toArray());
        $this->assertEquals(json_encode($expected), $name->toJSON());
    }
}
