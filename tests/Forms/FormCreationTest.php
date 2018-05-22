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
    }
}
