<?php

namespace Themosis\Tests\Forms\Forms;

use Themosis\Forms\Contracts\FormFactoryInterface;
use Themosis\Forms\Contracts\Formidable;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Fields\Types\EmailType;
use Themosis\Forms\Fields\Types\TextType;

class ContactForm implements Formidable
{
    /**
     * @var FormInterface
     */
    protected $form;

    public function build(FormFactoryInterface $factory): Formidable
    {
        $this->form = $factory->make()
            ->add(new TextType('name'))
            ->add(new EmailType('email'))
            ->get();

        return $this;
    }

    /**
     * Get the form.
     *
     * @return FormInterface
     */
    public function get(): FormInterface
    {
        return $this->form;
    }
}
