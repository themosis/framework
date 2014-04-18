<?php

use Themosis\Field\FieldException;
/**
 * Field_Test class.
 *
 * @extends WP_UnitTestCase
 */
class Field_Test extends WP_UnitTestCase{

    public function setUP()
    {
    	parent::setUp();
    }

    /**
     * compareAssociativeArrays function.
     *
     * @access private
     * @param array $a (default: array())
     * @param array $b (default: array())
     * @return void
     */
    private function compareAssociativeArrays($a = array(), $b = array())
    {
    	if (count(array_diff_assoc($a, $b))) {
            return false;
        }

        // we know that the indexes, but maybe not values, match.
        // compare the values between the two arrays
        foreach($a as $k => $v) {
            if ($v !== $b[$k]) {
                return false;
            }
        }

        return true;
    }

    /**
     * testFieldTextWithNoCorrectParameters function.
     *
     * @access public
     * @return void
     */
    public function testFieldTextWithNoCorrectParameters()
    {
        $count = 0;
        $arr = array(

    	    array(),
    	    45,
    	    3.6,
    	    true,
    	    array('something')

    	);

        foreach($arr as $value){
            try{

              	Field::text($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::text method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * testFieldTextMethodWithoutExtras function.
     *
     * @access public
     * @return void
     */
    public function testFieldTextMethodWithoutExtras()
    {
    	$f = Field::text('some-name');

        // Text methods returns only 2 arguments by default
        $this->assertEquals(2, count($f));

        // Compare returned values
        $arr = array(
            'type'  => 'text',
            'name'  => 'some-name'
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }


    /**
     * testFieldTextareaWithNoCorrectParameters function.
     *
     * @access public
     * @return void
     */
    public function testFieldTextareaWithNoCorrectParameters()
    {
        $count = 0;
        $arr = array(

    	    array(),
    	    45,
    	    3.6,
    	    true,
    	    array('something')

    	);

        foreach($arr as $value){
            try{

              	Field::textarea($value);

            } catch(FieldException $e){

                $count++;

                // Check the exception message
                $this->assertEquals('Invalid name parameter for Field::textarea method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * testFieldTextareaMethodWithoutExtras function.
     *
     * @access public
     * @return void
     */
    public function testFieldTextareaMethodWithoutExtras()
    {
    	$f = Field::textarea('some-name');

    	// Text methods returns only 2 arguments by default
        $this->assertEquals(2, count($f));

        // Compare returned values
        $arr = array(
            'type'  => 'textarea',
            'name'  => 'some-name'
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }


    /**
     * testFieldCheckboxWithNoCorrectParameters function.
     *
     * @access public
     * @return void
     */
    public function testFieldCheckboxWithNoCorrectParameters()
    {
        $count = 0;
        $arr = array(

    	    array(),
    	    45,
    	    3.6,
    	    true,
    	    array('something')

    	);

        foreach($arr as $value){
            try{

              	Field::checkbox($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::checkbox method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }


    /**
     * testFieldCheckboxMethodWithoutExtras function.
     *
     * @access public
     * @return void
     */
    public function testFieldCheckboxMethodWithoutExtras()
    {
    	$f = Field::checkbox('some-name');

    	// Text methods returns only 2 arguments by default
        $this->assertEquals(2, count($f));

        // Compare returned values
        $arr = array(
            'type'  => 'checkbox',
            'name'  => 'some-name'
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }

    /**
     * Test field 'Checkboxes' with invalid name parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldCheckboxesMethodWithInvalidNameParameters()
    {
        $count = 0;
        $arr = array(
            array(),
            45,
            3.6,
            true,
            array('something')
        );

        foreach($arr as $value){
            try{

                Field::checkboxes($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::checkboxes method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'checkboxes' field with invalid options parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldCheckboxesWithInvalidOptions()
    {
        $count = 0;
        $arr = array(
            24,
            'some-text',
            true
        );

        foreach($arr as $value){
            try{

                Field::checkboxes('colors', $value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('You need to pass a non-associative array of options as a second parameter for the Field::checkboxes method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'checkboxes' field without extras parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldCheckboxesMethodWithoutExtras()
    {
        $options = array(
            'value1',
            'value2',
            'value3'
        );

        $f = Field::checkboxes('some-name', $options);

        // Text methods returns only 2 arguments by default
        $this->assertEquals(3, count($f));

        // Compare returned values
        $arr = array(
            'type'      => 'checkboxes',
            'name'      => 'some-name',
            'options'   => $options
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }

    /**
     * Test 'radio' field with invalid name parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldRadioMethodWithInvalidNameParameters()
    {
        $count = 0;
        $arr = array(
            array(),
            45,
            3.6,
            true,
            array('something')
        );

        foreach($arr as $value){
            try{

                Field::radio($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::radio method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'radio' field with invalid options.
     *
     * @access public
     * @return void
     */
    public function testFieldRadioWithInvalidOptions()
    {
        $count = 0;
        $arr = array(
            24,
            'some-text',
            true
        );

        foreach($arr as $value){
            try{

                Field::radio('a-name', $value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('You need to pass a non-associative array of options as a second parameter for the Field::radio method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'radio' field without extra parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldRadioMethodWithoutExtras()
    {
        $options = array(
            'value1',
            'value2',
            'value3'
        );

        $f = Field::radio('some-name', $options);

        // Text methods returns only 2 arguments by default
        $this->assertEquals(3, count($f));

        // Compare returned values
        $arr = array(
            'type'      => 'radio',
            'name'      => 'some-name',
            'options'   => $options
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }

    /**
     * Test 'select' field with invalid name parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldSelectMethodWithInvalidNameParameters()
    {
        $count = 0;
        $arr = array(

            array(),
            45,
            3.6,
            true,
            array('something')

        );

        foreach($arr as $value){
            try{

                Field::select($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::select method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'select' field with invalid options.
     *
     * @access public
     * @return void
     */
    public function testFieldSelectWithInvalidOptions()
    {
        $count = 0;
        $arr = array(
            24,
            'some-text',
            true,
            array()
        );

        foreach($arr as $value){
            try{

                Field::select('select-name', $value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('You need to pass an array of options as a second parameter for the Field::select method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'select' field multiple invalid parameter.
     *
     * @access public
     * @return void
     */
    public function testFieldSelectMultipleInvalidParameter()
    {
        $count = 0;
        $params = array(
            'some string',
            '',
            array(),
            44,
            8.7
        );

        foreach($params as $p){
            try{

                Field::select('field-name', array(
                    'Option 1',
                    'Option 2',
                    'Option 3'
                ), $p);

            } catch(FieldException $e){
                $count++;
                $this->assertEquals('You need to pass a boolean as a third parameter for the Field::select method.', $e->getMessage());
            }
        }

        $this->assertEquals($count, count($params));
    }

    /**
     * Test 'select' field without extras parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldSelectMethodWithoutExtras()
    {
        $options = array(
            'value1',
            'value2',
            'value3'
        );

        $f = Field::select('field-slug', $options, true);
        $f2 = Field::select('select-field', $options);

        // Text methods returns only 4 arguments by default
        $this->assertEquals(4, count($f));
        $this->assertEquals(4, count($f2));

        // Compare returned values
        $arr = array(
            'type'      => 'select',
            'name'      => 'field-slug',
            'options'   => $options,
            'multiple'  => true
        );

        $arr2 = array(
            'type'      => 'select',
            'name'      => 'select-field',
            'options'   => $options,
            'multiple'  => false
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
        $this->assertTrue($this->compareAssociativeArrays($arr2, $f2));
    }

    /**
     * Test 'infinite' field with invalid name parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldInfiniteMethodWithInvalidNameParameters()
    {
        $count = 0;
        $arr = array(

            array(),
            45,
            3.6,
            true,
            array('something')

        );

        foreach($arr as $value){
            try{

                Field::infinite($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::infinite method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'infinite' field with invalid fields parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldInfiniteWithInvalidOptions()
    {
        $count = 0;
        $arr = array(
            24,
            'some-text',
            true,
            array()
        );

        foreach($arr as $value){
            try{

                Field::infinite('infinite-name', $value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('You need to pass an array of fields as a second parameter for the Field::infinite method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Tesst 'infinite' field without extras parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldInfiniteMethodWithoutExtras()
    {
        $fields = array(
            Field::text('name'),
            Field::textarea('content'),
            Field::radio('radio', array(1,2,3))
        );

        $f = Field::infinite('field-slug', $fields, true);

        // Text methods returns only 3 arguments by default
        $this->assertEquals(3, count($f));

        // Compare returned values
        $arr = array(
            'type'      => 'infinite',
            'name'      => 'field-slug',
            'fields'    => $fields
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }

    /**
     * Test 'media' field with invalid name parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldMediaWithInvalidNameParameters()
    {
        $count = 0;
        $arr = array(
            array(),
            45,
            3.6,
            true,
            array('something')
        );

        foreach($arr as $value){
            try{

                Field::media($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::media method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'media' field without extras parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldMediaMethodWithoutExtras()
    {
        $f = Field::media('media-slug');

        // Text methods returns only 2 arguments by default
        $this->assertEquals(2, count($f));

        // Compare returned values
        $arr = array(
            'type'  => 'media',
            'name'  => 'media-slug'
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }

    /**
     * Test 'editor' field with invalid name parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldEditorWithInvalidNameParameters()
    {
        $count = 0;
        $arr = array(
            array(),
            45,
            3.6,
            true,
            array('something')
        );

        foreach($arr as $value){
            try{

                Field::editor($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::editor method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'editor' field with invalid settings.
     *
     * @access public
     * @return void
     */
    public function testFieldEditorWithInvalidSettings()
    {
        $count = 0;
        $arr = array(
            24,
            'some-text',
            true,
            array()
        );

        foreach($arr as $value){
            try{

                Field::editor('editor', $value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid settings parameter for Field::editor method. Array expected as a second parameter.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr)-1, $count);
    }

    /**
     * Test 'editor' field without extras parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldEditorMethodWithoutExtras()
    {
        $f = Field::editor('editor-slug');

        // Text methods returns only 3 arguments by default
        $this->assertEquals(3, count($f));

        // Compare returned values
        $arr = array(
            'type'          => 'editor',
            'name'          => 'editor-slug',
            'editor_args'   => array()
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }

    /**
     * Test 'section' field with invalid name parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldSectionWithInvalidNameParameters()
    {
        $count = 0;
        $arr = array(
            array(),
            45,
            3.6,
            true,
            array('something')
        );

        foreach($arr as $value){
            try{

                Field::section($value);

            } catch(FieldException $e){

                $count++;
                $this->assertEquals('Invalid name parameter for Field::section method.', $e->getMessage());

            }
        }

        // Invalid values given
        $this->assertEquals(count($arr), $count);
    }

    /**
     * Test 'section' field without extra parameters.
     *
     * @access public
     * @return void
     */
    public function testFieldSectionMethodWithoutExtras()
    {
        $f = Field::section('section-slug');

        // Text methods returns only 2 arguments by default
        $this->assertEquals(2, count($f));

        // Compare returned values
        $arr = array(
            'type'  => 'section',
            'name'  => 'section-slug'
        );

        $this->assertTrue($this->compareAssociativeArrays($arr, $f));
    }
}

?>