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

        $this->assertEquals(['name' => 'th_firstname'], $firstname->getOptions());
        $this->assertEquals(['name' => 'th_lastname'], $lastname->getOptions());
        $this->assertEquals(['name' => 'th_email'], $email->getOptions());

        $this->expectException('\InvalidArgumentException');
        $firstname->setOptions(['name' => 'something']);
    }

    public function testCreateNewFormAndChangePropertiesAtRuntime()
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
        $this->assertEquals(['name' => 'wp_firstname'], $form->repository()->getField('firstname')->getOptions());
        $this->assertEquals(['name' => 'wp_email'], $form->repository()->getField('email')->getOptions());
        $this->assertEquals(['name' => 'wp_firstname'], $firstname->getOptions());
        $this->assertEquals(['name' => 'wp_email'], $email->getOptions());

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
    }
}
