<?php

namespace Themosis\Tests\Metabox;

use Illuminate\Config\Repository;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Forms\Fields\FieldsRepository;
use Themosis\Hook\ActionBuilder;
use Themosis\Hook\FilterBuilder;
use Themosis\Metabox\Contracts\MetaboxInterface;
use Themosis\Metabox\Factory;
use Themosis\Metabox\Resources\MetaboxResource;
use Themosis\Metabox\Resources\Transformers\MetaboxTransformer;
use Themosis\Support\Section;

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
            new FilterBuilder($this->getApplication()),
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
        $viewFactory = $this->getMockBuilder('Illuminate\View\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        return new \Themosis\Field\Factory($this->getApplication(), $viewFactory);
    }

    public function testCreateEmptyMetaboxWithDefaultArguments()
    {
        $factory = $this->getFactory();

        $box = $factory->make('properties');

        $this->assertInstanceOf(MetaboxInterface::class, $box);
        $this->assertEquals('properties', $box->getId());
        $this->assertEquals('Properties', $box->getTitle());
        $this->assertEquals('post', $box->getScreen());
        $this->assertEquals('normal', $box->getContext());
        $this->assertEquals('default', $box->getPriority());
        $this->assertEquals([$box, 'handle'], $box->getCallback());
        $this->assertTrue(is_array($box->getArguments()));
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

    public function testCreateMetaboxResourceWithNoFields()
    {
        $factory = $this->getFactory();

        $box = $factory->make('infos');

        $expected = [
            'id' => 'infos',
            'context' => 'normal',
            'l10n' => [],
            'locale' => 'en_US',
            'priority' => 'default',
            'screen' => [
                'id' => 'post',
                'post_type' => 'post'
            ],
            'title' => 'Infos',
            'fields' => [
                'data' => []
            ],
            'groups' => [
                'data' => []
            ]
        ];

        $this->assertEquals($expected, $box->toArray());
        $this->assertEquals(json_encode($expected), $box->toJson());
    }

    public function testCreateMetaboxResourceWithCustomFields()
    {
        $factory = $this->getFactory();
        $fields = $this->getFieldsFactory();

        $box = $factory->make('properties', 'page')
            ->setTitle('Book Properties')
            ->add($fields->text('author'));

        $expected = [
            'id' => 'properties',
            'context' => 'normal',
            'l10n' => [],
            'locale' => 'en_US',
            'priority' => 'default',
            'screen' => [
                'id' => 'page',
                'post_type' => 'page'
            ],
            'title' => 'Book Properties',
            'fields' => [
                'data' => [
                    [
                        'attributes' => [
                            'id' => 'th_author_field'
                        ],
                        'basename' => 'author',
                        'component' => 'themosis.fields.text',
                        'data_type' => '',
                        'default' => '',
                        'name' => 'th_author',
                        'options' => [
                            'group' => 'default',
                            'info' => '',
                            'l10n' => []
                        ],
                        'label' => [
                            'inner' => 'Author',
                            'attributes' => [
                                'for' => 'th_author_field'
                            ]
                        ],
                        'theme' => '',
                        'type' => 'text',
                        'validation' => [
                            'errors' => true,
                            'messages' => [],
                            'placeholder' => 'author',
                            'rules' => ''
                        ],
                        'value' => ''
                    ]
                ]
            ],
            'groups' => [
                'data' => [
                    [
                        'id' => 'default',
                        'theme' => '',
                        'title' => ''
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $box->toArray());
        $this->assertEquals(json_encode($expected), $box->toJson());
    }

    public function testAddSectionsAndFieldsToMetabox()
    {
        $factory = $this->getFactory();
        $fields = $this->getFieldsFactory();

        $box = $factory->make('options')
            ->add($fields->text('anything'))
            ->add(
                new Section('general', 'General', [
                    $fields->text('firstname'),
                    $fields->email('email')
                ])
            )
            ->add(
                new Section('social', 'Social', [
                    $fields->text('facebook'),
                    $fields->text('twitter')
                ])
            );

        $this->assertEquals(3, count($box->repository()->getGroups()));
        $this->assertEquals('default', $box->repository()->getGroup('default')->getId());
        $this->assertEquals('general', $box->repository()->getGroup('general')->getId());
        $this->assertEquals('social', $box->repository()->getGroup('social')->getId());

        $this->assertEquals(5, count($box->repository()->all()));
        $this->assertEquals(1, count($box->repository()->getGroup('default')->getItems()));
        $this->assertEquals(2, count($box->repository()->getGroup('general')->getItems()));
        $this->assertEquals(2, count($box->repository()->getGroup('social')->getItems()));
    }

    public function testAddTranslationsToMetabox()
    {
        $factory = $this->getFactory();

        $box = $factory->make('stuff');

        $box->addTranslation('hello', 'Hello World');

        $this->assertEquals('Hello World', $box->getTranslation('hello'));
        $this->assertEquals([
            'hello' => 'Hello World'
        ], $box->getTranslations());
    }

    public function testAddCapabilityToMetabox()
    {
        $factory = $this->getFactory();

        $box = $factory->make('anything');

        $box->setCapability('edit_posts');

        $this->assertEquals('edit_posts', $box->getCapability());
    }

    public function testAddTemplatesToMetabox()
    {
        $factory = $this->getFactory();

        $box = $factory->make('properties')
                ->setTemplate('fullwidth');

        $this->assertEquals(['page' => ['fullwidth']], $box->getTemplate());

        $box->setTemplate(['custom', 'left-sidebar', 'two-third']);

        $this->assertEquals(['page' => ['custom', 'left-sidebar', 'two-third']], $box->getTemplate());

        $box = $factory->make('shared', ['post', 'page', 'products'])
            ->setTemplate('fullwidth', 'post')
            ->setTemplate(['one', 'two'], 'custom');

        $this->assertEquals([
            'custom' => ['one', 'two'],
            'post' => ['fullwidth']
        ], $box->getTemplate());
    }
}
