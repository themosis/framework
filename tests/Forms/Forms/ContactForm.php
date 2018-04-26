<?php

namespace Themosis\Tests\Forms\Forms;

use Themosis\Forms\Fields\FieldBuilder;
use Themosis\Forms\Fields\Types\TextType;
use Themosis\Forms\Form;

class ContactForm extends Form
{
    public function configure(FieldBuilder $builder)
    {
        $this->add('name', TextType::class);
    }
}
