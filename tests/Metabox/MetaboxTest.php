<?php

namespace Themosis\Tests\Metabox;

use Illuminate\Config\Repository;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Forms\Fields\FieldsRepository;
use Themosis\Hook\ActionBuilder;
use Themosis\Metabox\Factory;
use Themosis\Metabox\MetaboxInterface;
use Themosis\Metabox\Resources\MetaboxResource;
use Themosis\Metabox\Resources\Transformers\MetaboxTransformer;

class MetaboxTest extends TestCase
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

    protected function getFactory()
    {
        return new Factory(
            $this->getApplication(),
            new ActionBuilder($this->getApplication()),
            $this->getMetaboxResource(),
            new FieldsRepository()
        );
    }

    protected function getMetaboxResource()
    {
        return new MetaboxResource(new Manager(), new ArraySerializer(), new MetaboxTransformer());
    }

    protected function getFieldsFactory()
    {
        return new \Themosis\Field\Factory($this->getApplication());
    }

    public function testCreateEmptyMetaboxWithDefaultArguments()
    {
        $factory = $this->getFactory();

        $box = $factory->make('properties');

        $this->assertInstanceOf(MetaboxInterface::class, $box);
        $this->assertEquals('properties', $box->getId());
        $this->assertEquals('Properties', $box->getTitle());
        $this->assertEquals('post', $box->getScreen());
        $this->assertEquals('advanced', $box->getContext());
        $this->assertEquals('default', $box->getPriority());
        $this->assertEquals([$box, 'handle'], $box->getCallback());
        $this->assertTrue(is_array($box->getArguments()));
        $this->assertTrue(empty($box->getArguments()));
        $this->assertEquals('default', $box->getLayout());
        $this->assertEquals('en_US', $box->getLocale());
        $this->assertEquals('th_', $box->getPrefix());
    }

    public function testCreateMetaboxWithCustomFields()
    {
        $factory = $this->getFactory();
        $fields = $this->getFieldsFactory();

        $box = $factory->make('properties')
            ->add($fields->text('name'))
            ->add($fields->email('email', [
                'group' => 'secondary'
            ]));

        $fieldName = $box->repository()->getField('name');
        $fieldEmail = $box->repository()->getField('email', 'secondary');

        $this->assertEquals('th_name', $fieldName->getName());
        $this->assertEquals('name', $fieldName->getBasename());
        $this->assertEquals('default', $fieldName->getOption('group'));

        $this->assertEquals('th_email', $fieldEmail->getName());
        $this->assertEquals('email', $fieldEmail->getBasename());
        $this->assertEquals('secondary', $fieldEmail->getOption('group'));
    }

    public function testCreateMetaboxResource()
    {
        $factory = $this->getFactory();

        $box = $factory->make('infos');

        $this->assertEquals([
            'id' => 'infos',
            'title' => 'Infos',
            'screen' => [
                'id' => 'post',
                'post_type' => 'post'
            ],
            'context' => 'advanced',
            'priority' => 'default'
        ], $box->toArray());
    }
}
