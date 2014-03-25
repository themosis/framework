<?php

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
    	    array(45),
    	    array(3.6),
    	    array(true),
    	    array(array('something'))

    	);

        foreach($arr as $value){
            try{

              	Field::text($value);

            } catch(Exception $e){

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
    	    array(45),
    	    array(3.6),
    	    array(true),
    	    array(array('something'))

    	);

        foreach($arr as $value){
            try{

              	Field::textarea($value);

            } catch(Exception $e){

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
    	    array(45),
    	    array(3.6),
    	    array(true),
    	    array(array('something'))

    	);

        foreach($arr as $value){
            try{

              	Field::checkbox($value);

            } catch(Exception $e){

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

}

?>