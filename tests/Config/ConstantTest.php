<?php


class ConstantTest extends PHPUnit_Framework_TestCase
{
    public function testConstantsAreDefined()
    {
        $constant = new \Themosis\Config\Constant([
            'one'   => 1,
            'TextDomain'    => 'themosis'
        ]);

        $constant->make();

        // Check if the constants are defined.
        $this->assertTrue(defined('ONE'));
        $this->assertEquals(1, ONE);

        // Second constant.
        $this->assertTrue(defined('TEXTDOMAIN'));
        $this->assertEquals('themosis', TEXTDOMAIN);
    }
}