<?php

/**
 * The Metabox class tests
 */
class Metabox_Test extends WP_UnitTestCase {


    /**
     * An unregistered Metabox with no default options
     *
     * @var mixed
     * @access private
     */
    private $box;


    /**
     * A registered Metabox with no "default" options and no fields
     *
     * @var mixed
     * @access private
     */
    private $registeredBox;

    public function setUp()
    {
        // Call WP_UnitTestCase setUp() method before.
    	parent::setUp();

    	// Create a simple Metabox without registering it.
    	$this->box = Metabox::make('Box Title', 'post');

    	// Create a registered Metabox with no options
    	$this->registeredBox = Metabox::make('Box Titlte', 'post')->set();

    }


    /**
     * testThrowsAnExceptionIfNoPametersInMakeMethod function.
     *
     * @expectedException Themosis\Metabox\MetaboxException
     */
    public function testThrowsAnExceptionIfNoPametersInMakeMethod()
    {
    	Metabox::make();
    }


    /**
     * testThrowsExceptionIfParametersAreNotStrings function.
     *
     * @expectedException Themosis\Metabox\MetaboxException
     */
    public function testThrowsExceptionIfParametersAreNotStrings()
    {
    	Metabox::make(42, array('post-slug'));
    }


    /**
     * testMetaboxMakeMethodReturnsAnInstance function.
     *
     * @access public
     * @return void
     */
    public function testMetaboxMakeMethodReturnsAnInstance()
    {
    	$box = Metabox::make('Title', 'page');
    	$this->assertContainsOnlyInstancesOf('Themosis\Metabox\Metabox', array($box));

    	$box = Metabox::make('Second Title', 'post', array(

    	    'context'   => 'normal',
    	    'priority'  => 'high'

    	));
    	$this->assertContainsOnlyInstancesOf('Themosis\Metabox\Metabox', array($box));
    }


    /**
     * testReturnsEmptyIfNoContextOrPriorityOptionsGiven function.
     *
     * @access public
     * @return void
     */
    public function testReturnsEmptyIfNoContextOrPriorityOptionsGiven()
    {
        $options = PHPUnit_Framework_Assert::readAttribute($this->box, 'options');

        $this->assertEmpty($options);
    }


    /**
     * testBuildAMetabox function.
     *
     * @access public
     * @return void
     */
    public function testBuildAMetabox()
    {
    	$box = $this->registeredBox;

    	$this->assertContainsOnlyInstancesOf('Themosis\Metabox\Metabox', array($box));
    }


}

?>