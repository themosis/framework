<?php

namespace Themosis\Tests\Forms;

use PHPUnit\Framework\TestCase;
use Themosis\Forms\Fields\Types\EmailType;
use Themosis\Forms\Fields\Types\TextType;
use Themosis\Forms\FormFactory;
use Themosis\Tests\Forms\Entities\ContactEntity;

class FormCreationTest extends TestCase
{
    public function testCreateNewForm()
    {
        $contact = new ContactEntity();
        $factory = new FormFactory();

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
        $factory = new FormFactory();

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
        $factory = new FormFactory();

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
}
