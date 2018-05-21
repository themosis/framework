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
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->get();

        $this->assertInstanceOf('Themosis\Forms\Form', $form);
    }
}
