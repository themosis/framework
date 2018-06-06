<?php

namespace Themosis\Tests\Forms;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Fields\Types\EmailType;
use Themosis\Forms\Fields\Types\PasswordType;
use Themosis\Forms\Fields\Types\TextareaType;
use Themosis\Forms\Fields\Types\TextType;
use Themosis\Forms\FormFactory;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\Tests\Forms\Entities\ContactEntity;
use Themosis\Tests\Forms\Forms\ContactForm;

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
                __DIR__.'/../../../framework/src/Themosis/Forms/views/',
                __DIR__.'/views/'
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

        $form = $factory->make([], $contact)
            ->add($firstname = new TextType('firstname'))
            ->add($lastname = new TextType('lastname'))
            ->add($email = new EmailType('email'))
            ->get();

        $this->assertInstanceOf('Themosis\Forms\Form', $form);

        $this->assertEquals('th_firstname', $firstname->getOptions('name'));
        $this->assertEquals('th_lastname', $lastname->getOptions('name'));
        $this->assertEquals('th_email', $email->getOptions('name'));

        /** @var $form FormInterface|FieldTypeInterface */
        $this->assertEquals([
            'method' => 'post'
        ], $form->getAttributes());
        $this->assertEquals('form.default', $form->getView());
        $this->assertEquals('form.group', $form->repository()->getGroup('default')->getView());

        $this->expectException('\InvalidArgumentException');
        $firstname->setOptions(['name' => 'something']);
    }

    public function testCreateNewFormAndChangePrefixAtRuntime()
    {
        $contact = new ContactEntity();
        $factory = $this->getFormFactory();

        $formBuilder = $factory->make([], $contact);
        $this->assertInstanceOf('Themosis\Forms\Contracts\FormBuilderInterface', $formBuilder);

        $form = $formBuilder->add($firstname = new TextType('firstname'))
            ->add($email = new EmailType('email'))
            ->get();

        // Change prefix of the form attached fields.
        /** @var $form FormInterface|FieldTypeInterface */
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

        $form = $factory->make([], $contact)
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

        $form = $factory->make([
            'attributes' => [
                'method' => 'get',
                'id' => 'get-form'
            ]
        ])
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

        $this->assertEquals([
            'method' => 'get',
            'id' => 'get-form'
        ], $form->getAttributes());

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

        $form->handleRequest($request);

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

    public function testFormHasAllDataForRendering()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make([
            'attributes' => [
                'id' => 'some-form',
                'name' => 'formidable'
            ]
        ])
            ->add($fn = new TextType('firstname'), [
                'attributes' => [
                    'class' => 'field branding',
                    'data-type' => 'text',
                    'required',
                    'id' => 'custom-id'
                ]
            ])
            ->add($em = new EmailType('email'), [
                'label' => 'Email Address',
                'label_attr' => [
                    'class' => 'label'
                ]
            ])
            ->get();

        $form->setView('forms.custom');
        $form->setGroupView('groups.custom');

        /** @var $form FormInterface|FieldTypeInterface */
        // Test form "data" only and not the output
        $this->assertEquals(1, count($form->repository()->getGroups()));
        $this->assertEquals([
            'class' => 'field branding',
            'data-type' => 'text',
            'required',
            'id' => 'custom-id'
        ], $fn->getOptions('attributes'));
        $this->assertEquals([
            'id' => 'th_email_field'
        ], $em->getOptions('attributes'));
        $this->assertEquals('Firstname', $fn->getOptions('label'));
        $this->assertEquals('Email Address', $em->getOptions('label'));
        $this->assertEquals([
            'for' => 'custom-id'
        ], $fn->getOptions('label_attr'));
        $this->assertEquals([
            'class' => 'label',
            'for' => 'th_email_field'
        ], $em->getOptions('label_attr'));

        $this->assertEquals('groups.custom', $form->repository()->getGroup($fn->getOptions('group'))->getView());
        $this->assertEquals('groups.custom', $form->repository()->getGroup($em->getOptions('group'))->getView());

        $this->assertEquals([
            'method' => 'post',
            'id' => 'some-form',
            'name' => 'formidable'
        ], $form->getAttributes());
        $this->assertEquals('forms.custom', $form->getView());

        $this->assertEquals('_themosisnonce', $form->getOptions('nonce'));
        $this->assertEquals('form', $form->getOptions('nonce_action'));
        $this->assertTrue($form->getOptions('referer'));

        $this->assertFalse($form->isRendered());
        $form->render();
        $this->assertTrue($form->isRendered());
    }

    public function testCreateFormFromAClass()
    {
        $class = new ContactForm();
        $form = $class->build($this->getFormFactory())->get();

        $this->assertInstanceOf(FormInterface::class, $form);
        $this->assertEquals(1, count($form->repository()->getGroups()));
        $this->assertInstanceOf(FieldTypeInterface::class, $form->repository()->getField('name'));
        $this->assertInstanceOf(FieldTypeInterface::class, $form->repository()->getField('email'));
    }

    public function testFormValuesOnSuccessfulSubmission()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($name = new TextType('name'))
            ->add($email = new EmailType('email'))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_name' => 'Marcel',
            'th_email' => 'marcel@domain.com'
        ]);

        $this->assertFalse($form->isValid());
        $this->assertEquals('', $name->getValue());
        $this->assertEquals('', $name->getRawValue());
        $this->assertEquals('', $email->getValue());
        $this->assertEquals('', $email->getRawValue());

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
        $this->assertEquals('Marcel', $name->getValue());
        $this->assertEquals('Marcel', $name->getRawValue());
        $this->assertEquals('marcel@domain.com', $email->getValue());
        $this->assertEquals('marcel@domain.com', $email->getRawValue());
    }

    public function testFormValuesOnFailingSubmission()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($name = new TextType('name'), [
                'rules' => 'min:5'
            ])
            ->add($email = new EmailType('email'), [
                'rules' => 'email'
            ])
            ->get();

        $request = Request::create('/', 'POST', [
            'th_name' => 'xxx',
            'th_email' => 'notanemail'
        ]);

        $this->assertEquals('', $name->getValue());
        $this->assertFalse($form->isValid());

        $form->handleRequest($request);

        $this->assertFalse($form->isValid());
        $this->assertEquals('', $name->getValue());
        $this->assertEquals('', $email->getValue());
    }

    public function testFormFieldTypesOnSuccessfulSubmission()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($name = new TextType('name'))
            ->add($email = new EmailType('email'))
            ->add($message = new TextareaType('message'))
            ->add($pass = new PasswordType('secret'))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_name' => 'Anyone',
            'th_email' => 'any@one.com',
            'th_message' => 'A very long message',
            'th_secret' => '1234'
        ]);

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
        $this->assertEquals('Anyone', $name->getValue());
        $this->assertEquals('any@one.com', $email->getValue());
        $this->assertEquals('A very long message', $message->getValue());
        $this->assertEquals('1234', $pass->getValue());
    }
}
