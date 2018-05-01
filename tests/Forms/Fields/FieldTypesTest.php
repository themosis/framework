<?php

namespace Themosis\Tests\Forms\Fields;

use PHPUnit\Framework\TestCase;
use Themosis\Forms\Fields\Types\TextType;
use Themosis\Html\HtmlBuilder;

class FieldTypesTest extends TestCase
{
    public function testTextTypeField()
    {
        $textField = new TextType(new HtmlBuilder());

        $this->assertEquals('<input type="text">', $textField->toHTML());

        $textField['name'] = 'fullname';
        $this->assertEquals('<input type="text" name="fullname">', $textField->toHTML());

        $textField['value'] = 'John Doe';
        $this->assertEquals('<input type="text" name="fullname" value="John Doe">', $textField->toHTML());
    }

    public function testTextTypeFieldWithCustomHtml()
    {
        $textfield = new TextType(new HtmlBuilder());
        $textfield['name'] = 'fullname';

        $this->assertEquals(
            '<div class="col"><label>Fullname</label><input type="text" name="fullname"></div>',
            $textfield->toHTML(function ($field) {
                return '<div class="col"><label>Fullname</label><input type="text"'.$field->attributes().'></div>';
            })
        );
    }
}
