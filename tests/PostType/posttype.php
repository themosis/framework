<?php

/**
 * An example test case.
 */
class PostType_Test extends WP_UnitTestCase {

    /**
     * An example test.
     *
     * We just want to make sure that false is still false.
     */
    function testIsEqual() {

        $this->assertEquals('hi', 'hi');
        $this->assertFalse(false);
    }
}

?>