<?php

namespace Themosis\Tests\Forms;

use Illuminate\Config\Repository;
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
use League\Fractal\Manager;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Fields\Types\TextType;
use Themosis\Forms\Form;
use Themosis\Forms\FormFactory;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\Tests\Forms\Entities\ContactEntity;
use Themosis\Tests\Forms\Forms\ContactForm;
use Themosis\Tests\Forms\Resources\Data\ContactFormRequestData;
use Themosis\Tests\Forms\Resources\Data\CreateArticleData;

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

        $this->application->bind('config', function () {
            $config = new Repository();
            $config->set('app.locale', 'en_US');

            return $config;
        });

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
            __DIR__ . '/../storage/views',
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
                __DIR__ . '/../../../framework/src/Forms/views/',
                __DIR__ . '/views/',
            ], ['blade.php', 'php']),
            new Dispatcher($application),
        );

        $factory->addExtension('blade', $resolver);
        $factory->setContainer($application);

        $this->viewFactory = $factory;

        return $factory;
    }

    protected function getFormFactory($locale = 'en_US')
    {
        return new FormFactory(
            $this->getValidationFactory($locale),
            $this->getViewFactory(),
            new Manager(),
            new \Themosis\Forms\Resources\Factory(),
        );
    }

    protected function getFieldsFactory()
    {
        return new \Themosis\Field\Factory($this->getApplication(), $this->getViewFactory());
    }

    public function testCreateNewForm()
    {
        $contact = new ContactEntity();
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make($contact)
            ->add($firstname = $fields->text('firstname'))
            ->add($lastname = $fields->text('lastname'))
            ->add($email = $fields->email('email'))
            ->get();

        $this->assertInstanceOf('Themosis\Forms\Form', $form);

        $this->assertEquals('th_firstname', $firstname->getName());
        $this->assertEquals('th_lastname', $lastname->getName());
        $this->assertEquals('th_email', $email->getName());

        /** @var $form FormInterface|FieldTypeInterface */
        $this->assertEquals([
            'method' => 'post',
        ], $form->getAttributes());
        $this->assertEquals('themosis.form.default', $form->getView());
        $this->assertEquals('themosis.form.group', $form->repository()->getGroup('default')->getView(true));
    }

    public function testCreateNewFormAndChangePrefixAtRuntime()
    {
        $contact = new ContactEntity();
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $formBuilder = $factory->make($contact);
        $this->assertInstanceOf('Themosis\Forms\Contracts\FormBuilderInterface', $formBuilder);

        $form = $formBuilder->add($firstname = $fields->text('firstname'))
            ->add($email = $fields->email('email'))
            ->get();

        // Change prefix of the form attached fields.
        /** @var $form FormInterface|FieldTypeInterface */
        $form->setPrefix('wp_');
        $this->assertEquals('wp_firstname', $form->repository()->getField('firstname')->getName());
        $this->assertEquals('wp_email', $form->repository()->getField('email')->getName());
        $this->assertEquals('wp_firstname', $firstname->getName());
        $this->assertEquals('wp_email', $email->getName());

        // Check fields attached to "default" group.
        $this->assertEquals(2, count($form->repository()->getFieldsByGroup('default')));
    }

    public function testCreateFormWithMultipleGroups()
    {
        $contact = new ContactEntity();
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make($contact)
                ->add($firstname = $fields->text('firstname'))
                ->add($lastname = $fields->text('lastname'))
                ->add($email = $fields->email('email', [
                    'group' => 'corporate',
                ]))
                ->add($company = $fields->text('company', [
                    'group' => 'corporate',
                ]))
                ->get();

        $this->assertEquals('default', $firstname->getOption('group'));
        $this->assertEquals('default', $lastname->getOption('group'));
        $this->assertEquals('corporate', $email->getOption('group'));
        $this->assertEquals('corporate', $company->getOption('group'));

        // We check if the repository is correctly storing fields by group.
        $this->assertEquals(2, count($form->repository()->getFieldsByGroup('default')));
        $this->assertEquals(2, count($form->repository()->getFieldsByGroup('corporate')));
        $this->assertEquals([
                $email,
                $company,
            ], $form->repository()->getFieldsByGroup('corporate')->getItems());

        // Form instance should be aware of its groups.
        $this->assertEquals(2, count($form->repository()->getGroups()));

        $groups = $form->repository()->getGroups();

        foreach ($groups as $group) {
            /** @var SectionInterface $group */
            $this->assertTrue(in_array($group->getId(), [
                'default',
                'corporate',
            ]));
        }
    }

    public function testCreateFormAndValidateUsingValidData()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make(null, [
            'attributes' => [
                'method' => 'get',
                'id' => 'get-form',
            ],
        ])
            ->add($firstname = $fields->text('firstname', [
                'rules' => 'required|min:3',
            ]))
            ->add($email = $fields->email('email', [
                'rules' => 'required|email',
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'Foo',
            'th_email' => 'foo@bar.com',
        ]);

        $this->assertEquals([
            'method' => 'get',
            'id' => 'get-form',
        ], $form->getAttributes());

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
    }

    public function testCreateFormWithErrorsMessages()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($fields->text('firstname', [
                'rules' => 'required|max:5',
                'messages' => [
                    'max' => 'The :attribute can only have 5 characters maximum.',
                    'required' => 'The firstname is required.',
                ],
            ]))
            ->add($fields->email('email', [
                'rules' => 'required|email',
            ]))
            ->add($fields->text('company', [
                'rules' => 'required',
                'messages' => [
                    'required' => 'The :attribute name is required.',
                ],
                'placeholder' => 'enterprise',
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'Marcel',
            'th_email' => 'marcel@domain.com',
        ]);

        $form->handleRequest($request);

        $this->assertFalse($form->isValid());
        $this->assertEquals(
            'The firstname can only have 5 characters maximum.',
            $form->error('firstname', true),
        );
        $this->assertEquals(
            'The enterprise name is required.',
            $form->error('company', true),
        );
    }

    public function testFormHasAllDataForRendering()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make(null, [
            'attributes' => [
                'id' => 'some-form',
                'name' => 'formidable',
            ],
        ])
            ->add($fn = $fields->text('firstname', [
                'attributes' => [
                    'class' => 'field branding',
                    'data-type' => 'text',
                    'required',
                    'id' => 'custom-id',
                ],
            ]))
            ->add($em = $fields->email('email', [
                'label' => 'Email Address',
                'label_attr' => [
                    'class' => 'label',
                ],
            ]))
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
            'id' => 'custom-id',
        ], $fn->getOption('attributes'));
        $this->assertEquals([
            'id' => 'th_email_field',
        ], $em->getOption('attributes'));
        $this->assertEquals('Firstname', $fn->getOption('label'));
        $this->assertEquals('Email Address', $em->getOption('label'));
        $this->assertEquals([
            'for' => 'custom-id',
        ], $fn->getOption('label_attr'));
        $this->assertEquals([
            'class' => 'label',
            'for' => 'th_email_field',
        ], $em->getOption('label_attr'));

        $this->assertEquals('themosis.groups.custom', $form->repository()->getGroup($fn->getOption('group'))->getView(true));
        $this->assertEquals('themosis.groups.custom', $form->repository()->getGroup($em->getOption('group'))->getView(true));

        $this->assertEquals([
            'method' => 'post',
            'id' => 'some-form',
            'name' => 'formidable',
        ], $form->getAttributes());
        $this->assertEquals('themosis.forms.custom', $form->getView());

        $this->assertEquals('_themosisnonce', $form->getOption('nonce'));
        $this->assertEquals('form', $form->getOption('nonce_action'));
        $this->assertTrue($form->getOption('referer'));

        $this->assertFalse($form->isRendered());
        $form->render();
        $this->assertTrue($form->isRendered());
    }

    public function testCreateFormFromAClass()
    {
        $class = new ContactForm();
        $form = $class->build($this->getFormFactory(), $this->getFieldsFactory());

        $this->assertInstanceOf(FormInterface::class, $form);
        $this->assertEquals(1, count($form->repository()->getGroups()));
        $this->assertInstanceOf(FieldTypeInterface::class, $form->repository()->getField('name'));
        $this->assertInstanceOf(FieldTypeInterface::class, $form->repository()->getField('email'));
    }

    public function testFormValuesOnSuccessfulSubmission()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($name = $fields->text('name'))
            ->add($email = $fields->email('email'))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_name' => 'Marcel',
            'th_email' => 'marcel@domain.com',
        ]);

        $this->assertFalse($form->isValid());
        $this->assertEquals('', $name->getValue());
        $this->assertEquals('', $name->getRawValue());
        $this->assertEquals('', $email->getValue());
        $this->assertEquals('', $email->getRawValue());

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
        $this->assertEquals('Marcel', $name->getValue());
        $this->assertEquals('', $name->getRawValue());
        $this->assertEquals('marcel@domain.com', $email->getValue());
        $this->assertEquals('', $email->getRawValue());
    }

    public function testFormValuesOnFailingSubmission()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($name = $fields->text('name', [
                'rules' => 'min:5',
            ]))
            ->add($email = $fields->email('email', [
                'rules' => 'email',
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_name' => 'xxx',
            'th_email' => 'notanemail',
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
        $fields = $this->getFieldsFactory();

        $form = $factory->make(null, [
            'flush' => false,
        ])
            ->add($name = $fields->text('name'))
            ->add($email = $fields->text('email'))
            ->add($message = $fields->textarea('message'))
            ->add($pass = $fields->password('secret'))
            ->add($num = $fields->integer('age'))
            ->add($price = $fields->number('price'))
            ->add($enable = $fields->checkbox('enable'))
            ->add($subscribe = $fields->checkbox('subscribe'))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_name' => 'Anyone',
            'th_email' => 'any@one.com',
            'th_message' => 'A very long message',
            'th_secret' => '1234',
            'th_age' => 32,
            'th_price' => 24.99,
            'th_enable' => 'yes',
            'th_subscribe' => 'no',
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
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($color = $fields->choice('colors', [
                'choices' => ['red', 'green', 'blue'],
            ]))
            ->add($country = $fields->choice('country', [
                'choices' => [
                    'Allemagne' => 'de',
                    'Belgique' => 'be',
                    'France' => 'fr',
                ],
                'multiple' => true,
            ]))
            ->add($groupedCountry = $fields->choice('group_country', [
                'choices' => [
                    'Europe' => [
                        'Allemagne' => 'de',
                        'Belgique' => 'be',
                        'France' => 'fr',
                    ],
                    'America' => [
                        'Canada' => 'ca',
                        'United States' => 'us',
                        'Mexico' => 'mx',
                    ],
                ],
                'expanded' => true,
            ]))
            ->add($anotherGroupCountry = $fields->choice('another_country', [
                'choices' => [
                    'Europe' => [
                        'de',
                        'be',
                        'fr',
                    ],
                    'America' => [
                        'ca',
                        'us',
                        'mx',
                    ],
                ],
                'multiple' => true,
            ]))
            ->add($article = $fields->choice('article', [
                'choices' => [
                    'Title 1' => 24,
                    'Title 2' => 456,
                    'Title XYZ' => 10,
                ],
            ]))
            ->add($post = $fields->choice('post', [
                'choices' => [
                    35,
                    7,
                    986,
                ],
            ]))
            ->add($featured = $fields->choice('featured', [
                'choices' => [
                    'Politics' => [
                        'Article 23' => 34,
                        'Article 35' => 78,
                    ],
                    'Tech' => [
                        'Article 67' => 89,
                        'Article 12' => 17,
                    ],
                ],
                'multiple' => true,
                'expanded' => true,
            ]))
            ->get();

        $request = Request::create('/', 'GET', [
            'th_colors' => 'green',
        ]);

        $form->handleRequest($request);

        $this->assertEquals([
            'Red' => 'red',
            'Green' => 'green',
            'Blue' => 'blue',
        ], $color->getOption('choices')->format()->get());
        $this->assertEquals('select', $color->getLayout());
        $this->assertEquals('green', $color->getValue());

        $this->assertEquals([
            'Allemagne' => 'de',
            'Belgique' => 'be',
            'France' => 'fr',
        ], $country->getOption('choices')->format()->get());
        $this->assertEquals('select', $country->getLayout());

        $this->assertEquals([
            'Europe' => [
                'Allemagne' => 'de',
                'Belgique' => 'be',
                'France' => 'fr',
            ],
            'America' => [
                'Canada' => 'ca',
                'United States' => 'us',
                'Mexico' => 'mx',
            ],
        ], $groupedCountry->getOption('choices')->format()->get());
        $this->assertEquals('radio', $groupedCountry->getLayout());

        $this->assertEquals([
            'Europe' => [
                'De' => 'de',
                'Be' => 'be',
                'Fr' => 'fr',
            ],
            'America' => [
                'Ca' => 'ca',
                'Us' => 'us',
                'Mx' => 'mx',
            ],
        ], $anotherGroupCountry->getOption('choices')->format()->get());
        $this->assertEquals('select', $anotherGroupCountry->getLayout());
        $this->assertTrue(in_array('multiple', $anotherGroupCountry->getAttributes()));

        $this->assertEquals([
            'Title 1' => 24,
            'Title 2' => 456,
            'Title XYZ' => 10,
        ], $article->getOption('choices')->format()->get());

        $this->assertEquals([
            '35' => 35,
            '7' => 7,
            '986' => 986,
        ], $post->getOption('choices')->format()->get());

        $this->assertEquals([
            'Politics' => [
                'Article 23' => 34,
                'Article 35' => 78,
            ],
            'Tech' => [
                'Article 67' => 89,
                'Article 12' => 17,
            ],
        ], $featured->getOption('choices')->format()->get());
        $this->assertEquals('checkbox', $featured->getLayout());
    }

    public function testChoiceTypeWithCheckboxLayout()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($featured = $fields->choice('featured', [
                'choices' => [
                    'Politics' => [
                        'Article 23' => 34,
                        'Article 35' => 78,
                    ],
                    'Tech' => [
                        'Article 67' => 89,
                        'Article 12' => 17,
                    ],
                ],
                'multiple' => true,
                'expanded' => true,
            ]))->get();

        $request = Request::create('/', 'POST', [
            'th_featured' => [78],
        ]);

        $form->handleRequest($request);

        $this->assertEquals([78], $featured->getValue());
    }

    public function testFormFlushFieldsValuesOnSubmissionSuccess()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($firstname = $fields->text('firstname', [
                'rules' => 'required|min:3',
            ]))
            ->add($email = $fields->email('email', [
                'rules' => 'required|email',
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'Marcel',
            'th_email' => 'marcel@example.com',
        ]);

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
        $this->assertFalse($form->isNotValid());
        $this->assertEquals($request->get($firstname->getName()), $firstname->getValue());
        $this->assertEquals($request->get($email->getName()), $email->getValue());

        $failingRequest = Request::create('/', 'POST', [
            'th_firstname' => 'Gilbert',
            'th_email' => 'notworkingemail',
        ]);

        $form->handleRequest($failingRequest);

        $this->assertFalse($form->isValid());
        $this->assertEquals($failingRequest->get($firstname->getName()), $firstname->getValue());
        $this->assertEmpty($email->getValue());
    }

    public function testFormFieldsHaveErrorsMessagesOnFailingSubmission()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($firstname = $fields->text('firstname', [
                'rules' => 'required|min:3|max:20|string',
                'messages' => [
                    'min' => 'The :attribute must be at least 3 characters long.',
                    'string' => 'The :attribute must be a string.',
                ],

            ]))
            ->add($email = $fields->email('email', [
                'rules' => 'required|email',
                'messages' => [
                    'email' => 'The :attribute must be a valid email address.',
                ],
            ]))
            ->add($message = $fields->textarea('message', [
                'rules' => 'string',
                'messages' => [
                    'string' => 'The :attribute must be text.',
                ],
            ]))
            ->add($subscribe = $fields->checkbox('subscribe', [
                'rules' => 'accepted',
                'messages' => [
                    'accepted' => 'The :attribute option must be checked.',
                ],
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 3,
            'th_email' => 'notanemail',
            'th_message' => 34,
        ]);

        $form->handleRequest($request);

        $this->assertEquals([
            'The firstname must be at least 3 characters long.',
            'The firstname must be a string.',
        ], $firstname->error());

        $this->assertEquals([
            'The email must be a valid email address.',
        ], $email->error());

        $this->assertEquals('The message must be text.', $message->error($message->getName(), true));

        $this->assertEquals('The subscribe option must be checked.', $subscribe->error($subscribe->getName(), true));
    }

    public function testFormGlobalErrorPropertyIsPassedToTheFields()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($firstname = $fields->text('firstname'))
            ->add($email = $fields->email('email'))
            ->add($message = $fields->textarea('message', [
                'errors' => false,
            ]))->get();

        $this->assertTrue($firstname->getOption('errors'));
        $this->assertTrue($email->getOption('errors'));
        $this->assertFalse($message->getOption('errors'));
    }

    public function testFormTheming()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($firstname = $fields->text('firstname'))
            ->add($email = $fields->email('email'))
            ->get();

        // Test default 'themosis' theme.
        $this->assertEquals('themosis', $form->getTheme());
        $this->assertEquals('themosis.form.default', $form->getView());
        $this->assertEquals('themosis', $firstname->getTheme());

        $form = $factory->make()
            ->add(new TextType('firstname'))
            ->get();

        $this->assertEquals('themosis', $form->getTheme());

        // Test custom form theme.
        $form = $factory->make(null, [
            'theme' => 'bootstrap',
        ])
            ->add($firstname = $fields->text('firstname'))
            ->add($email = $fields->email('email', [
                'theme' => 'themosis',
            ]))
            ->get();

        $this->assertEquals('bootstrap', $form->getTheme());
        $this->assertEquals('bootstrap.form.default', $form->getView());

        $this->assertEquals('bootstrap.form.group', $form->repository()->getGroup('default')->getView(true));

        $this->assertEquals('bootstrap', $firstname->getTheme());
        $this->assertEquals('bootstrap.types.text', $firstname->getView());

        $this->assertEquals('themosis', $email->getTheme());
        $this->assertEquals('themosis.types.email', $email->getView());
    }

    public function testFormOpenAndClosingTags()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->get();

        $this->assertTrue($form->getOption('tags'));

        $form = $factory->make(null, [
            'tags' => false,
        ])
            ->get();

        $this->assertFalse($form->getOption('tags'));
    }

    public function testFormHiddenFieldType()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($update = $fields->hidden('update'))
            ->add($action = $fields->hidden('action', [
                'data' => 'something',
            ]))
            ->get();

        $this->assertEquals('th_action', $action->getName());
        $this->assertEquals($action, $form->repository()->getFieldByName('action'));
        $this->assertEmpty($form->repository()->getFieldByName('update')->getValue());
        $this->assertEquals('something', $form->repository()->getFieldByName('action')->getValue());
    }

    public function testFormCreationWithFieldFactory()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($firstname = ($fields->text('firstname'))->setPrefix('wp_')->setView('custom'))
            ->add($email = $fields->email('email', [
                'data' => 'me@example.com',
            ]))
            ->add($message = $fields->textarea('message'))
            ->add($colors = $fields->choice('colors', [
                'choices' => [
                    'Rouge' => 'red',
                    'Vert' => 'green',
                    'Bleu' => 'blue',
                ],
            ]))
            ->add($subject = $fields->text('subject', [
                'rules' => 'required',
            ]))
            ->get();

        $form->setPrefix('xy_');

        $request = Request::create('/', 'post', [
            'xy_colors' => 'green',
            'xy_email' => 'email@example.com',
        ]);

        $form->handleRequest($request);

        $this->assertEquals($firstname, $form->repository()->getFieldByName('firstname'));
        $this->assertEquals('xy_', $firstname->getPrefix());
        $this->assertEquals('xy_', $form->getPrefix());
        $this->assertEquals($email, $form->repository()->getFieldByName('email'));
        $this->assertEquals('xy_', $email->getPrefix());
        $this->assertEquals('xy_email', $email->getName());
        $this->assertEquals('email@example.com', $email->getValue());
        $this->assertEquals('en_US', $email->getLocale());
        $this->assertEquals('themosis.types.email', $email->getView());
        $this->assertEquals('themosis.custom', $firstname->getView());
        $this->assertEquals($message, $form->repository()->getFieldByName('message'));
        $this->assertEquals('xy_', $message->getPrefix());
        $this->assertEquals('green', $colors->getValue());

        $this->assertFalse($form->isValid());
    }

    public function testFormFieldsInfoProperty()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($fields->text('name'))
            ->add($fields->email('email', [
                'info' => 'Insert a valid email address.',
            ]))
            ->add($fields->textarea('message', [
                'info' => '<strong>HTML</strong>',
            ]))
            ->get();

        $this->assertEmpty($form->repository()->getField('name')->getOption('info'));
        $this->assertEquals('Insert a valid email address.', $form->repository()->getField('email')->getOption('info'));
        $this->assertEquals('<strong>HTML</strong>', $form->repository()->getField('message')->getOption('info'));
    }

    public function testFormFieldsTypeProperty()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $factory->make()
            ->add($name = $fields->text('name'))
            ->add($email = $fields->email('email', [
                'data_type' => 'string',
            ]))
            ->add($colors = $fields->choice('colors', [
                'choices' => [
                    'Rouge' => 'red',
                    'Vert' => 'green',
                    'Bleu' => 'blue',
                ],
                'expanded' => true,
                'multiple' => true,
                'data_type' => 'array',
            ]))
            ->get();

        $this->assertTrue(is_null($name->getOption('data_type')));
        $this->assertEquals('string', $email->getOption('data_type'));
        $this->assertEquals('array', $colors->getOption('data_type'));
    }

    protected function expected(array $data = [])
    {
        return array_merge([
            'attributes' => [
                'method' => 'post',
            ],
            'flush' => true,
            'locale' => 'en_US',
            'nonce' => '_themosisnonce',
            'referer' => true,
            'tags' => true,
            'theme' => 'themosis',
            'type' => 'form',
            'validation' => [
                'errors' => true,
                'isValid' => false,
            ],
        ], $data);
    }

    public function testFormWithoutFieldsToJSON()
    {
        $factory = $this->getFormFactory();

        /** @var FieldTypeInterface $form */
        $form = $factory->make()->get();

        $this->assertEquals($this->expected([
            'fields' => [
                'data' => [],
            ],
            'groups' => [
                'data' => [],
            ],
        ]), $form->toArray());
        $this->assertEquals(json_encode($this->expected([
            'fields' => [
                'data' => [],
            ],
            'groups' => [
                'data' => [],
            ],
        ])), $form->toJson());
    }

    public function testFormWithFieldsToJSON()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make()
            ->add($fields->text('firstname'))
            ->get();

        $this->assertEquals($this->expected([
            'fields' => [
                'data' => [
                    [
                        'attributes' => [
                            'id' => 'th_firstname_field',
                        ],
                        'basename' => 'firstname',
                        'component' => 'themosis.fields.text',
                        'data_type' => '',
                        'default' => '',
                        'name' => 'th_firstname',
                        'options' => [
                            'group' => 'default',
                            'info' => '',
                            'l10n' => [],
                        ],
                        'label' => [
                            'inner' => 'Firstname',
                            'attributes' => [
                                'for' => 'th_firstname_field',
                            ],
                        ],
                        'theme' => 'themosis',
                        'type' => 'text',
                        'validation' => [
                            'errors' => true,
                            'messages' => [],
                            'placeholder' => 'firstname',
                            'rules' => '',
                        ],
                        'value' => '',
                    ],
                ],
            ],
            'groups' => [
                'data' => [
                    [
                        'id' => 'default',
                        'theme' => 'themosis',
                        'title' => '',
                    ],
                ],
            ],
        ]), $form->toArray());
        $this->assertEquals(json_encode($this->expected([
            'fields' => [
                'data' => [
                    [
                        'attributes' => [
                            'id' => 'th_firstname_field',
                        ],
                        'basename' => 'firstname',
                        'component' => 'themosis.fields.text',
                        'data_type' => '',
                        'default' => '',
                        'name' => 'th_firstname',
                        'options' => [
                            'group' => 'default',
                            'info' => '',
                            'l10n' => [],
                        ],
                        'label' => [
                            'inner' => 'Firstname',
                            'attributes' => [
                                'for' => 'th_firstname_field',
                            ],
                        ],
                        'theme' => 'themosis',
                        'type' => 'text',
                        'validation' => [
                            'errors' => true,
                            'messages' => [],
                            'placeholder' => 'firstname',
                            'rules' => '',
                        ],
                        'value' => '',
                    ],
                ],
            ],
            'groups' => [
                'data' => [
                    [
                        'id' => 'default',
                        'theme' => 'themosis',
                        'title' => '',
                    ],
                ],
            ],
        ])), $form->toJson());
    }

    public function testFormWithValidationToJSON()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make(null, ['flush' => false])
            ->add($fields->text('firstname'))
            ->add($fields->email('email'))
            ->add($fields->textarea('message'))
            ->add($fields->choice('colors', [
                'choices' => [
                    'red',
                    'green',
                    'blue',
                ],
            ]))
            ->add($fields->choice('sizes', [
                'choices' => [
                    'Small' => 10,
                    'Medium' => 20,
                    'Large' => 30,
                ],
                'multiple' => true,
            ]))
            ->add($fields->submit('register', [
                'data' => 'Contact us',
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'Nathan',
            'th_email' => 'nathan@example.org',
            'th_message' => 'Marco Polo',
            'th_colors' => 'green',
            'th_sizes' => [20, 30],
        ]);

        $form->handleRequest($request);

        $resource = $form->toArray();

        $this->assertTrue($resource['validation']['isValid']);
        $this->assertEquals('Nathan', $resource['fields']['data'][0]['value']);
        $this->assertEquals('nathan@example.org', $resource['fields']['data'][1]['value']);
        $this->assertEquals('Marco Polo', $resource['fields']['data'][2]['value']);
        $this->assertEquals('green', $resource['fields']['data'][3]['value']);
        $this->assertEquals([20, 30], $resource['fields']['data'][4]['value']);
        $this->assertEquals(
            ['key' => 'Small', 'value' => 10, 'type' => 'option'],
            $resource['fields']['data'][4]['options']['choices'][0],
        );
    }

    public function testFormGetDataObjectDefaultValues()
    {
        $dto = new CreateArticleData();
        $dto->title = 'Hello World';
        $dto->setAuthor('Marcel Proust');

        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make($dto)
            ->add($title = $fields->text('title'))
            ->add($content = $fields->textarea('content'))
            ->add($author = $fields->text('author'))
            ->get();

        $this->assertEquals($dto->title, $form->repository()->getFieldByName($title->getBaseName())->getValue());
        $this->assertEquals($dto->getAuthor(), $form->repository()->getFieldByName($author->getBaseName())->getValue());
    }

    public function testFormSetDataObjectValuesWithValidRequest()
    {
        $dto = new ContactFormRequestData();

        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make($dto)
            ->add($name = $fields->text('fullname'))
            ->add($email = $fields->email('email'))
            ->add($message = $fields->textarea('message'))
            ->add($checkbox = $fields->checkbox('subscribe'))
            ->add($follow = $fields->checkbox('follow'))
            ->add($colors = $fields->choice('colors', [
                'choices' => [
                    'red',
                    'green',
                    'blue',
                ],
                'multiple' => true,
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_fullname' => 'Dae Doe',
            'th_email' => 'julien@corporation.xyz',
            'th_message' => 'This is a short message with very little details.',
            'th_subscribe' => 'on',
            'th_colors' => ['green', 'blue'],
        ]);

        $form->handleRequest($request);

        $this->assertTrue($form->isValid());
        $this->assertEquals($request->get($name->getName()), $dto->getFullname());
        $this->assertEquals($request->get($email->getName()), $dto->getEmail());
        $this->assertEquals($request->get($message->getName()), $dto->getMessage());
        $this->assertTrue($dto->getSubscribe());
        $this->assertFalse($dto->getFollow());
        $this->assertEquals($request->get($colors->getName()), $dto->getColors());
    }

    public function testFormUseDataObjectDefaultValuesAndSetRequestValuesOnSuccess()
    {
        $dto = new CreateArticleData();
        $dto->title = 'Hello World';

        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make($dto)
            ->add($title = $fields->text('title'))
            ->add($content = $fields->textarea('content'))
            ->add($author = $fields->text('author'))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_title' => 'Goodbye World',
            'th_content' => 'Another short content.',
            'th_author' => 'Joe Koe',
        ]);

        $form->handleRequest($request);

        $this->assertEquals($request->get($title->getName()), $dto->title);
        $this->assertEquals($request->get($content->getName()), $dto->content);
        $this->assertEquals($request->get($author->getName()), $dto->getAuthor());
    }

    public function testFormUseDataObjectDefaultValuesAndManageRequestValuesOnFailure()
    {
        $dto = new CreateArticleData();
        $dto->setAuthor('Marcel');

        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make($dto)
            ->add($fields->text('title', [
                'rules' => 'required|string',
            ]))
            ->add($content = $fields->textarea('content', [
                'rules' => 'required|min:6',
            ]))
            ->add($fields->text('author', [
                'rules' => 'required|string',
            ]))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_title' => '',
            'th_content' => 'Something',
            'th_author' => 25,
        ]);

        $form->handleRequest($request);

        $this->assertEmpty($dto->title);
        $this->assertEquals($request->get($content->getName()), $dto->content);
        $this->assertEmpty($dto->getAuthor());
    }

    public function test_form_open_close_tag()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        /** @var Form $form */
        $form = $factory->make(null, ['tags' => false])
            ->add($fields->text('test'))
            ->get();

        $this->assertEmpty($form->open());
        $this->assertEmpty($form->close());
    }

    public function test_checkbox_field_type_with_default_value_can_be_overriden()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();

        $form = $factory->make(new CheckboxDto())
            ->add($fields->checkbox('subscribe'))
            ->get();

        $form->handleRequest(Request::create('/', 'POST', ['th_subscribe' => false]));

        $attributes = $form->repository()->getFieldByName('subscribe')->getAttributes();

        // The default value is "true", so the field should be checked.
        // But the request sent a "false" value, so we need to make sure the
        // checked attribute is no longer set.
        $this->assertFalse(isset($attributes['checked']));
    }

    public function test_valdidator_return_no_value_on_fail()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();
        $field = $fields->text('firstname', [
            'rules' => 'required|max:3',
        ]);

        $form = $factory->make()
            ->add($field)
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'John',
        ]);

        $form->handleRequest($request);

        $this->assertFalse($form->isValid());

        $this->assertEmpty($field->getValue());
    }

    public function test_valdidator_return_value_on_fail()
    {
        $factory = $this->getFormFactory();
        $fields = $this->getFieldsFactory();
        $field = $fields->text('firstname', [
            'rules' => 'required|max:3',
        ]);

        $form = $factory
            ->make(null, [
                'flushOnFail' => false,
            ])
            ->add($field)
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => $value = 'John',
        ]);

        $form->handleRequest($request);

        $this->assertFalse($form->isValid());

        $this->assertEquals($value, $field->getValue());
    }
}

class CheckboxDto
{
    public $subscribe = 'on';
}
