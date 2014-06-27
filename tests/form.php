<?php

class Form_Test extends WP_UnitTestCase
{
    /**
     * Test the Form::close() method.
     *
     * @return void
     */
    public function testFormCloseTag()
    {
        $tag = Form::close();
        $output = '</form>';
        $this->assertEquals($output, $tag);
    }

    /**
     * Test Form::label() method.
     *
     * @return void
     */
    public function testFormLabel()
    {
        $label1 = Form::label('themosis', 'Themosis');
        $output1 = '<label for="themosis">Themosis</label>';

        $this->assertEquals($output1, $label1);

        $label2 = Form::label('themosis', 'Themosis', array('class' => 'large'));
        $output2 = '<label class="large" for="themosis">Themosis</label>';

        $this->assertEquals($output2, $label2);
    }

} 