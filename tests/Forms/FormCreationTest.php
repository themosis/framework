<?php

namespace Themosis\Tests\Forms;

use PHPUnit\Framework\TestCase;
use Themosis\Forms\FormFactory;

class FormCreationTest extends TestCase
{
    public function testCreateNewForm()
    {
        $factory = new FormFactory();

        $form = $factory->make();

        $this->assertInstanceOf('Themosis\Forms\Form', $form);
    }
}
