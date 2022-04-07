<?php

namespace Themosis\Tests\Forms\Forms;

use Themosis\Field\Contracts\FieldFactoryInterface;
use Themosis\Forms\Contracts\FormFactoryInterface;
use Themosis\Forms\Contracts\Formidable;
use Themosis\Forms\Contracts\FormInterface;

class ContactForm implements Formidable
{
    /**
     * @var FormInterface
     */
    protected $form;

    public function build(FormFactoryInterface $factory, FieldFactoryInterface $fields): FormInterface
    {
        return $factory->make()
            ->add($fields->text('name'))
            ->add($fields->email('email'))
            ->get();
    }
}
