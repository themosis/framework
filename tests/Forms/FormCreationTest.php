<?php

namespace Themosis\Tests\Forms;

use PHPUnit\Framework\TestCase;
use Themosis\Forms\FormFactory;

class FormCreationTest extends TestCase
{
    public function testFormFactoryNamespaces()
    {
        $factory = new FormFactory('Themosis\Tests\Forms\Forms');

        $this->assertTrue(in_array('Themosis\Tests\Forms\Forms', $factory->getNamespaces()));

        $factory->addNamespaces('Some\Namespace');
        $factory->addNamespaces(['App\Forms', 'OtherApp\Forms']);

        $this->assertTrue(in_array('Some\Namespace', $factory->getNamespaces()));
        $this->assertTrue(in_array('App\Forms', $factory->getNamespaces()));
        $this->assertTrue(in_array('OtherApp\Forms', $factory->getNamespaces()));
    }

    public function testFormIsCreated()
    {
        $factory = new FormFactory('Themosis\Tests\Forms\Forms');
        $form = $factory->make('ContactForm');

        $this->assertInstanceOf('Themosis\Forms\Form', $form);
        $this->assertEquals('<form method="post"></form>', $form->render());

        $form2 = $factory->make('ContactForm');
        $this->assertTrue($form2 !== $form);
        $form2->setAttributes([
            'action' => '/',
            'method' => 'post'
        ]);
        $this->assertEquals('<form action="/" method="post"></form>', $form2->render());
    }
}
