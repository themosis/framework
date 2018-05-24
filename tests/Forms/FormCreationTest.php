<?php

namespace Themosis\Tests\Forms;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Forms\Fields\Types\EmailType;
use Themosis\Forms\Fields\Types\TextType;
use Themosis\Forms\FormFactory;
use Themosis\Tests\Forms\Entities\ContactEntity;

class FormCreationTest extends TestCase
{
    protected function getValidationFactory()
    {
        $application = new Application();
        $translator = new Translator(new FileLoader(new Filesystem(), ''), 'en');

        return new Factory($translator, $application);
    }

    protected function getFormFactory()
    {
        return new FormFactory($this->getValidationFactory());
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
            'email' => $email,
            'company' => $company
        ], $form->repository()->getFieldsByGroup('corporate'));

        // Form instance should be aware of its groups.
        $this->assertEquals(2, count($form->repository()->getGroups()));
        $this->assertEquals([
            'default',
            'corporate'
        ], $form->repository()->getGroups());
    }

    public function testCreateFormAndValidate()
    {
        $factory = $this->getFormFactory();

        $form = $factory->make()
            ->add(new TextType('firstname'))
            ->add(new EmailType('lastname'))
            ->get();

        $request = Request::create('/', 'POST', [
            'th_firstname' => 'Foo',
            'th_email' => 'foo@bar.com'
        ]);

        $form->handleRequest($request);

        //$this->assertTrue($form->isValid());
    }
}
