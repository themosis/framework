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
use Themosis\Forms\Fields\Types\CheckboxType;
use Themosis\Forms\Fields\Types\ChoiceType;
use Themosis\Forms\Fields\Types\EmailType;
use Themosis\Forms\Fields\Types\HiddenType;
use Themosis\Forms\Fields\Types\IntegerType;
use Themosis\Forms\Fields\Types\NumberType;
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

    protected function getValidationFactory($locale)
    {
        $translator = new Translator(new FileLoader(new Filesystem(), ''), $locale);

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

    protected function getFormFactory($locale = 'en_US')
    {
        return new FormFactory($this->getValidationFactory($locale), $this->getViewFactory());
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
        $this->assertEquals('themosis.form.default', $form->getView());
        $this->assertEquals('themosis.form.group', $form->repository()->getGroup('default')->getView());

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

        $this->assertEquals('themosis.groups.custom', $form->repository()->getGroup($fn->getOptions('group'))->getView());
        $this->assertEquals('themosis.groups.custom', $form->repository()->getGroup($em->getOptions('group'))->getView());

        $this->assertEquals([
            'method' => 'post',
            'id' => 'some-form',
            'name' => 'formidable'
        ], $form->getAttributes());
        $this->assertEquals('themosis.forms.custom', $form->getView());

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

        $form = $factory->make([
            'flush' => false
        ])
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

    public function testFormBasicFieldTypesOnSuccessfulSubmission()
    {
        $factory = $this->getFormFactory('fr_FR');

        $form = $factory->make([
            'flush' => false
        ])
            ->add($name = new TextType('name'))
            ->add($email = new EmailType('email'))
            ->add($message = new TextareaType('message'))
            ->add($pass = new PasswordType('secret'))
            ->add($num = new IntegerType('age'))
            ->add($price = new NumberType('price'))
            ->add($enable = new CheckboxType('enable'))
            ->add($subscribe = new CheckboxType('subscribe'))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_name' => 'Anyone',
            'th_email' => 'any@one.com',
            'th_message' => 'A very long message',
            'th_secret' => '1234',
            'th_age' => 32,
            'th_price' => 24.99,
            'th_enable' => 'yes',
            'th_subscribe' => 'no'
        ]);

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
        $this->assertEquals('Anyone', $name->getValue());
        $this->assertEquals('any@one.com', $email->getValue());
        $this->assertEquals('A very long message', $message->getValue());
        $this->assertEquals('1234', $pass->getValue());
        $this->assertEquals(32, $num->getValue());
        $this->assertTrue(is_string($num->getValue()));
        $this->assertTrue(32 === $num->getRawValue());
        $this->assertTrue(is_int($num->getRawValue()));
        $this->assertEquals('24,99', $price->getValue());
        $this->assertFalse(is_numeric($price->getValue()));
        $this->assertEquals(24.99, $price->getRawValue());
        $this->assertTrue($enable->getValue());
        $this->assertEquals('on', $enable->getRawValue());
        $this->assertFalse($subscribe->getValue());
        $this->assertEquals('off', $subscribe->getRawValue());
    }

    public function testFormWithChoiceTypeFields()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make([
            'flush' => false
        ])
            ->add($color = new ChoiceType('colors'), [
                'choices' => ['red', 'green', 'blue']
            ])
            ->add($country = new ChoiceType('country'), [
                'choices' => [
                    'Allemagne' => 'de',
                    'Belgique' => 'be',
                    'France' => 'fr'
                ],
                'multiple' => true
            ])
            ->add($groupedCountry = new ChoiceType('group_country'), [
                'choices' => [
                    'Europe' => [
                        'Allemagne' => 'de',
                        'Belgique' => 'be',
                        'France' => 'fr'
                    ],
                    'America' => [
                        'Canada' => 'ca',
                        'United States' => 'us',
                        'Mexico' => 'mx'
                    ]
                ],
                'expanded' => true
            ])
            ->add($anotherGroupCountry = new ChoiceType('another_country'), [
                'choices' => [
                    'Europe' => [
                        'de',
                        'be',
                        'fr'
                    ],
                    'America' => [
                        'ca',
                        'us',
                        'mx'
                    ]
                ],
                'multiple' => true
            ])
            ->add($article = new ChoiceType('article'), [
                'choices' => [
                    'Title 1' => 24,
                    'Title 2' => 456,
                    'Title XYZ' => 10
                ]
            ])
            ->add($post = new ChoiceType('post'), [
                'choices' => [
                    35,
                    7,
                    986
                ]
            ])
            ->add($featured = new ChoiceType('featured'), [
                'choices' => [
                    'Politics' => [
                        'Article 23' => 34,
                        'Article 35' => 78
                    ],
                    'Tech' => [
                        'Article 67' => 89,
                        'Article 12' => 17
                    ]
                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->get();

        $request = Request::create('/', 'GET', [
            'th_colors' => 'green'
        ]);

        $form->handleRequest($request);

        $this->assertEquals([
            'Red' => 'red',
            'Green' => 'green',
            'Blue' => 'blue'
        ], $color->getOptions('choices')->format()->get());
        $this->assertEquals('select', $color->getLayout());
        $this->assertEquals('green', $color->getValue());

        $this->assertEquals([
            'Allemagne' => 'de',
            'Belgique' => 'be',
            'France' => 'fr'
        ], $country->getOptions('choices')->format()->get());
        $this->assertEquals('select', $country->getLayout());

        $this->assertEquals([
            'Europe' => [
                'Allemagne' => 'de',
                'Belgique' => 'be',
                'France' => 'fr'
            ],
            'America' => [
                'Canada' => 'ca',
                'United States' => 'us',
                'Mexico' => 'mx'
            ]
        ], $groupedCountry->getOptions('choices')->format()->get());
        $this->assertEquals('radio', $groupedCountry->getLayout());

        $this->assertEquals([
            'Europe' => [
                'De' => 'de',
                'Be' => 'be',
                'Fr' => 'fr'
            ],
            'America' => [
                'Ca' => 'ca',
                'Us' => 'us',
                'Mx' => 'mx'
            ]
        ], $anotherGroupCountry->getOptions('choices')->format()->get());
        $this->assertEquals('select', $anotherGroupCountry->getLayout());
        $this->assertTrue(in_array('multiple', $anotherGroupCountry->getAttributes()));

        $this->assertEquals([
            'Title 1' => 24,
            'Title 2' => 456,
            'Title XYZ' => 10
        ], $article->getOptions('choices')->format()->get());

        $this->assertEquals([
            '35' => 35,
            '7' => 7,
            '986' => 986
        ], $post->getOptions('choices')->format()->get());

        $this->assertEquals([
            'Politics' => [
                'Article 23' => 34,
                'Article 35' => 78
            ],
            'Tech' => [
                'Article 67' => 89,
                'Article 12' => 17
            ]
        ], $featured->getOptions('choices')->format()->get());
        $this->assertEquals('checkbox', $featured->getLayout());
    }

    public function testChoiceTypeWithCheckboxLayout()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make([
            'flush' => false
        ])
            ->add($featured = new ChoiceType('featured'), [
                'choices' => [
                    'Politics' => [
                        'Article 23' => 34,
                        'Article 35' => 78
                    ],
                    'Tech' => [
                        'Article 67' => 89,
                        'Article 12' => 17
                    ]
                ],
                'multiple' => true,
                'expanded' => true
            ])->get();

        $request = Request::create('/', 'POST', [
            'th_featured' => [78]
        ]);

        $form->handleRequest($request);

        $this->assertEquals([78], $featured->getValue());
    }

    public function testFormFlushFieldsValuesOnSubmissionSuccess()
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
            'th_firstname' => 'Marcel',
            'th_email' => 'marcel@example.com'
        ]);

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
        $this->assertFalse($form->isNotValid());
        $this->assertEmpty($firstname->getValue());
        $this->assertEmpty($email->getValue());

        $failingRequest = Request::create('/', 'POST', [
            'th_firstname' => 'Gilbert',
            'th_email' => 'notworkingemail'
        ]);

        $form->handleRequest($failingRequest);

        $this->assertFalse($form->isValid());
        $this->assertEquals($failingRequest->get($firstname->getName()), $firstname->getValue());
        $this->assertEmpty($email->getValue());
    }

    public function testFormFieldsHaveErrorsMessagesOnFailingSubmission()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($firstname = new TextType('firstname'), [
                'rules' => 'required|min:3|max:20|string',
                'messages' => [
                    'min' => 'The :attribute must be at least 3 characters long.',
                    'string' => 'The :attribute must be a string.'
                ]

            ])
            ->add($email = new EmailType('email'), [
                'rules' => 'required|email',
                'messages' => [
                    'email' => 'The :attribute must be a valid email address.'
                ]
            ])
            ->add($message = new TextareaType('message'), [
                'rules' => 'string',
                'messages' => [
                    'string' => 'The :attribute must be text.'
                ]
            ])
            ->add($subscribe = new CheckboxType('subscribe'), [
                'rules' => 'accepted',
                'messages' => [
                    'accepted' => 'The :attribute option must be checked.'
                ]
            ])
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 3,
            'th_email' => 'notanemail',
            'th_message' => 34
        ]);

        $form->handleRequest($request);

        $this->assertEquals([
            'The firstname must be at least 3 characters long.',
            'The firstname must be a string.'
        ], $firstname->error());

        $this->assertEquals([
            'The email must be a valid email address.'
        ], $email->error());

        $this->assertEquals('The message must be text.', $message->error($message->getName(), true));

        $this->assertEquals('The subscribe option must be checked.', $subscribe->error($subscribe->getName(), true));
    }

    public function testFormGlobalErrorPropertyIsPassedToTheFields()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($firstname = new TextType('firstname'))
            ->add($email = new EmailType('email'))
            ->add($message = new TextareaType('message'), [
                'errors' => false
            ])
            ->get();

        $this->assertTrue($firstname->getOptions('errors'));
        $this->assertTrue($email->getOptions('errors'));
        $this->assertFalse($message->getOptions('errors'));
    }

    public function testFormTheming()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($firstname = new TextType('firstname'))
            ->add($email = new EmailType('email'))
            ->get();

        // Test default 'themosis' theme.
        $this->assertEquals('themosis', $form->getOptions('theme'));
        $this->assertEquals('themosis.form.default', $form->getView());
        $this->assertEquals('themosis', $firstname->getOptions('theme'));

        $form = $factory->make([
            'theme' => ''
        ])
            ->add(new TextType('firstname'))
            ->get();

        $this->assertEquals('themosis', $form->getOptions('theme'));

        // Test custom form theme.
        $form = $factory->make([
            'theme' => 'bootstrap'
        ])
            ->add($firstname = new TextType('firstname'))
            ->add($email = new EmailType('email'), [
                'theme' => 'themosis'
            ])
            ->get();

        $this->assertEquals('bootstrap', $form->getOptions('theme'));
        $this->assertEquals('bootstrap.form.default', $form->getView());

        $this->assertEquals('bootstrap.form.group', $form->repository()->getGroup('default')->getView());

        $this->assertEquals('bootstrap', $firstname->getOptions('theme'));
        $this->assertEquals('bootstrap.types.text', $firstname->getView());

        $this->assertEquals('themosis', $email->getOptions('theme'));
        $this->assertEquals('themosis.types.email', $email->getView());
    }

    public function testFormOpenAndClosingTags()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->get();

        $this->assertTrue($form->getOptions('tags'));

        $form = $factory->make([
            'tags' => false
        ])
            ->get();

        $this->assertFalse($form->getOptions('tags'));
    }

    public function testFormHiddenFieldType()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add($update = new HiddenType('update'))
            ->add($action = new HiddenType('action'), [
                'data' => 'something'
            ])
            ->get();

        $this->assertEquals('th_action', $action->getName());
        $this->assertEquals($action, $form->repository()->getFieldByName('action'));
        $this->assertEmpty($form->repository()->getFieldByName('update')->getValue());
        $this->assertEquals('something', $form->repository()->getFieldByName('action')->getValue());
    }
}
