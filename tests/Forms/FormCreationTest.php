<?php

namespace Themosis\Tests\Forms;

use PHPUnit\Framework\TestCase;

class FormCreationTest extends TestCase
{
    public function testFormIsCreated()
    {
        $factory = new \Themosis\Forms\FormFactory();
        $form = $factory->make();

        $this->assertInstanceOf('Themosis\Forms\Form', $form);
        $this->assertEquals('<form method="post"></form>', $form->render());

        $form2 = $factory->make();
        $this->assertTrue($form2 !== $form);
        $form2->setAttributes([
            'action' => '/',
            'method' => 'post'
        ]);
        $this->assertEquals('<form action="/" method="post"></form>', $form2->render());
    }
}
