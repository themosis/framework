<?php

namespace Themosis\Tests\Forms\Fields;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Report\Text;
use Themosis\Forms\Fields\Types\CheckboxType;
use Themosis\Forms\Fields\Types\NumberType;
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

    public function testTextTypeFieldWithDefaultValue()
    {
        $textfield = new TextType(new HtmlBuilder());
        $textfield->setName('somename');
        $textfield->setDefaultValue('A default val');

        $this->assertEquals(
            '<input type="text" name="somename" value="A default val">',
            $textfield->toHTML()
        );

        $textfield = new TextType(new HtmlBuilder());
        $textfield->setDefaultValue(new \stdClass());

        $this->assertEquals(
            '<input type="text">',
            $textfield->toHTML()
        );
    }

    public function testNumberTypeFieldWithDefaultValue()
    {
        $field = new NumberType(new HtmlBuilder());
        $field->setName('total');
        $field->setDefaultValue(45);

        $this->assertEquals(
            '<input type="number" name="total" value="45">',
            $field->toHTML()
        );

        $field = new NumberType(new HtmlBuilder());
        $field->setDefaultValue(['Wrong value type']);

        $this->assertEquals(
            '<input type="number">',
            $field->toHTML()
        );
    }

    public function testCheckboxTypeField()
    {
        $field = new CheckboxType(new HtmlBuilder());

        $this->assertEquals(
            '<input type="checkbox">',
            $field->toHTML()
        );

        $field = new CheckboxType(new HtmlBuilder());
        $field->setName('support');

        $this->assertEquals(
            '<input type="checkbox" name="support">',
            $field->toHTML()
        );

        $field = new CheckboxType(new HtmlBuilder());
        $field->setName('newsletter');
        $field->setDefaultValue('subscribe');

        $this->assertEquals(
            '<input type="checkbox" name="newsletter" value="subscribe">',
            $field->toHTML()
        );

        // Multiple options.
        $field = new CheckboxType(new HtmlBuilder());
        $field->setName('colours');
        $field->setOptions([
            'red',
            'green',
            'blue'
        ]);

        $this->assertEquals(
            '<input type="checkbox" name="colours" value="red"><input type="checkbox" name="colours" value="green"><input type="checkbox" name="colours" value="blue">',
            $field->toHTML()
        );

        // Multiple options with label.
        $field = new CheckboxType(new HtmlBuilder());
        $field->setName('colours');
        $field->setOptions([
            'red' => 'Rouge',
            'green' => 'Vert',
            'blue' => 'Bleu'
        ]);

        $this->assertEquals(
            '<input type="checkbox" name="colours" value="red" id="f_red"><label for="f_red">Rouge</label><input type="checkbox" name="colours" value="green" id="f_green"><label for="f_green">Vert</label><input type="checkbox" name="colours" value="blue" id="f_blue"><label for="f_blue">Bleu</label>',
            $field->toHTML()
        );
    }
}
