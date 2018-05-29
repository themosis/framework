<?php

namespace Themosis\Tests\Forms;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Forms\Fields\Types\EmailType;
use Themosis\Forms\Fields\Types\TextType;
use Themosis\Forms\FormFactory;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\Tests\Forms\Entities\ContactEntity;

class FormCreationTest extends TestCase
{
    protected $application;

    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $viewFactory;

    protected function getApplication()
    {
        if (! is_null($this->application)) {
            return $this->application;
        }

        $this->application = new Application();

        return $this->application;
    }

    protected function getValidationFactory()
    {
        $translator = new Translator(new FileLoader(new Filesystem(), ''), 'en');

        return new Factory($translator, $this->getApplication());
    }

    protected function getViewFactory()
    {
        if (! is_null($this->viewFactory)) {
            return $this->viewFactory;
        }

        $application = $this->getApplication();

        $filesystem = new Filesystem();

        $bladeCompiler = new BladeCompiler(
            $filesystem,
            __DIR__.'/../storage/views'
        );
        $application->instance('blade', $bladeCompiler);

        $resolver = new EngineResolver();

        $resolver->register('php', function () {
            return new PhpEngine();
        });

        $resolver->register('blade', function () use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });

        $factory = new \Illuminate\View\Factory(
            $resolver,
            $viewFinder = new FileViewFinder($filesystem, [
                __DIR__.'/../../../framework/src/Themosis/Forms/views/'
            ], ['blade.php', 'php']),
            new Dispatcher($application)
        );

        $factory->addExtension('blade', $resolver);
        $factory->setContainer($application);

        $this->viewFactory = $factory;

        return $factory;
    }

    protected function getFormFactory()
    {
        return new FormFactory($this->getValidationFactory(), $this->getViewFactory());
    }

    public function testCreateNewForm()
    {
        $contact = new ContactEntity();
        $factory = $this->getFormFactory();

        $form = $factory->make($contact)
            ->add($firstname = new TextType('firstname'))
            ->add($lastname = new TextType('lastname'))
            ->add($email = new EmailType('email'))
            ->get();

        $this->assertInstanceOf('Themosis\Forms\Form', $form);

        $this->assertEquals('th_firstname', $firstname->getOptions('name'));
        $this->assertEquals('th_lastname', $lastname->getOptions('name'));
        $this->assertEquals('th_email', $email->getOptions('name'));

        $this->expectException('\InvalidArgumentException');
        $firstname->setOptions(['name' => 'something']);
    }

    public function testCreateNewFormAndChangePrefixAtRuntime()
    {
        $contact = new ContactEntity();
        $factory = $this->getFormFactory();

        $formBuilder = $factory->make($contact);
        $this->assertInstanceOf('Themosis\Forms\Contracts\FormBuilderInterface', $formBuilder);

        $form = $formBuilder->add($firstname = new TextType('firstname'))
            ->add($email = new EmailType('email'))
            ->get();

        // Change prefix of the form attached fields.
        $form->setPrefix('wp_');
        $this->assertEquals('wp_firstname', $form->repository()->getField('firstname')->getOptions('name'));
        $this->assertEquals('wp_email', $form->repository()->getField('email')->getOptions('name'));
        $this->assertEquals('wp_firstname', $firstname->getOptions('name'));
        $this->assertEquals('wp_email', $email->getOptions('name'));

        // Check fields attached to "default" group.
        $this->assertEquals(2, count($form->repository()->getFieldsByGroup('default')));
    }

    public function testCreateFormWithMultipleGroups()
    {
        $contact = new ContactEntity();
        $factory = $this->getFormFactory();

        $form = $factory->make($contact)
                ->add($firstname = new TextType('firstname'))
                ->add($lastname = new TextType('lastname'))
                ->add($email = new EmailType('email'), [
                    'group' => 'corporate'
                ])
                ->add($company = new TextType('company'), [
                    'group' => 'corporate'
                ])
                ->get();

        $this->assertEquals('default', $firstname->getOptions('group'));
        $this->assertEquals('default', $lastname->getOptions('group'));
        $this->assertEquals('corporate', $email->getOptions('group'));
        $this->assertEquals('corporate', $company->getOptions('group'));

        // We check if the repository is correctly storing fields by group.
        $this->assertEquals(2, count($form->repository()->getFieldsByGroup('default')));
        $this->assertEquals(2, count($form->repository()->getFieldsByGroup('corporate')));
        $this->assertEquals([
                $email,
                $company
            ], $form->repository()->getFieldsByGroup('corporate')->getItems());

        // Form instance should be aware of its groups.
        $this->assertEquals(2, count($form->repository()->getGroups()));

        $groups = $form->repository()->getGroups();

        foreach ($groups as $group) {
            /** @var SectionInterface $group */
            $this->assertTrue(in_array($group->getId(), [
                'default',
                'corporate'
            ]));
        }
    }

    public function testCreateFormAndValidateUsingValidData()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($firstname = new TextType('firstname'), [
                'rules' => 'required|min:3'
            ])
            ->add($email = new EmailType('email'), [
                'rules' => 'required|email'
            ])
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'Foo',
            'th_email' => 'foo@bar.com'
        ]);

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
    }

    public function testCreateFormWithErrorsMessages()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add(new TextType('firstname'), [
                'rules' => 'required|max:5',
                'messages' => [
                    'max' => 'The :attribute can only have 5 characters maximum.',
                    'required' => 'The firstname is required.'
                ]
            ])
            ->add(new EmailType('email'), [
                'rules' => 'required|email'
            ])
            ->add(new TextType('company'), [
                'rules' => 'required',
                'messages' => [
                    'required' => 'The :attribute name is required.'
                ],
                'placeholder' => 'enterprise'
            ])
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'Marcel',
            'th_email' => 'marcel@domain.com'
        ]);

        try {
            $form->handleRequest($request);
        } catch (ValidationException $exception) {
            $this->assertFalse($form->isValid());
            $this->assertEquals(
                'The firstname can only have 5 characters maximum.',
                $form->error('firstname', true)
            );
            $this->assertEquals(
                'The enterprise name is required.',
                $form->error('company', true)
            );
        }
    }

    public function testFormHasAllDataForRendering()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add(new TextType('firstname'))
            ->add(new EmailType('email'))
            ->get();

        // Test form "data" only and not the output
        $this->assertEquals(
            $this->viewFactory->make('form.default')->render(),
            $form->render()
        );
    }
}
