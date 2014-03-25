<?php

/**
 * An example test case.
 */
class PostType_Test extends WP_UnitTestCase {


    /**
     * A PostType object
     *
     * @var object
     * @access public
     */
    private $post;

    public function setUp()
    {
        // Call WP_UnitTestCase setUp() method before.
    	parent::setUp();

    	// Create a correct custom post type and make it available to the class.
    	$this->post = PostType::make('thfmk-book', 'Books');
    }

    /**
     * testExceptionThrownIfNoParameters function.
     *
     * @expectedException Themosis\PostType\PostTypeException
     */
    public function testExceptionThrownIfNoParameters()
    {
    	$post = PostType::make();
    }


    /**
     * testExceptionThrownIfOnlyFirstParameter function.
     *
     * @expectedException Themosis\PostType\PostTypeException
     */
    public function testExceptionThrownIfOnlyFirstParameter()
    {
    	$post = PostType::make('my-slug');
    }


    /**
     * ttestReturnACustomPostTypeInstance function.
     *
     * @access public
     * @return void
     */
    public function testReturnACustomPostTypeInstance() {

        $post = PostType::make('thf-books', 'Books');

        $this->assertContainsOnlyInstancesOf('Themosis\PostType\PostType', array($post));

    }


    /**
     * testExceptionThrownIfNoStringParametersGiven function.
     *
     * @expectedException Themosis\PostType\PostTypeException
     */
    public function testExceptionThrownIfIntParametersGiven()
    {
    	$post = PostType::make(24);
    }


    /**
     * testExceptionThrownIfArrayParameterGiven function.
     *
     * @expectedException Themosis\PostType\PostTypeException
     */
    public function testExceptionThrownIfArrayParameterGiven()
    {
    	$post = PostType::make(array('value'), array(true));
    }


    /**
     * testAttributesOfANewCustomPostType function.
     *
     * @access public
     * @return void
     */
    public function testAttributesOfANewCustomPostType()
    {
        // Check the slug attribute
    	$this->assertClassHasAttribute('slug', 'Themosis\PostType\PostType');

    	// Check the data attribute
    	$this->assertClassHasAttribute('data', 'Themosis\PostType\PostType');

    	// Check the event attribute
    	$this->assertClassHasAttribute('event', 'Themosis\PostType\PostType');

    	// Check the mediaEvent attribute
    	$this->assertClassHasAttribute('mediaEvent', 'Themosis\PostType\PostType');
    }


    /**
     * testPostTypeIsSlugIsAString function.
     *
     * @access public
     * @return void
     */
    public function testPostTypeIsSlugIsAString()
    {
    	$this->assertEquals('thfmk-book', $this->post->getSlug());
    }


    /**
     * testDataAttributeHasPostTypeDataObject function.
     *
     * @access public
     * @return void
     */
    public function testDataAttributeHasPostTypeDataObject()
    {
        $data = $this->post->getData();

        $this->assertContainsOnlyInstancesOf('Themosis\PostType\PostTypeData', array($data));
    }


    /**
     * testPostTypeDateReturnAnArrayOfCustomPostTypeArguments function.
     *
     * @access public
     * @return void
     */
    public function testPostTypeDateReturnAnArrayOfCustomPostTypeArguments()
    {
    	$datas = $this->post->getData()->getArgs();

    	$this->assertTrue(is_array($datas));
    }


    /**
     * testEventAttributeHasActionObject function.
     *
     * @access public
     * @return void
     */
    public function testEventAttributeHasActionObject()
    {
    	$this->assertContainsOnlyInstancesOf('Themosis\Action\Action', array(PHPUnit_Framework_Assert::readAttribute($this->post, 'event')));
    }


    /**
     * testMediaEventAttributeHasActionObject function.
     *
     * @access public
     * @return void
     */
    public function testMediaEventAttributeHasActionObject()
    {
    	$this->assertContainsOnlyInstancesOf('Themosis\Action\Action', array(PHPUnit_Framework_Assert::readAttribute($this->post, 'mediaEvent')));
    }


    /**
     * testCallToSetMethodWithOrWithoutParametersReturnsThePostTypeInstance function.
     *
     * @access public
     * @return void
     */
    public function testCallToSetMethodWithOrWithoutParametersReturnsThePostTypeInstance()
    {
        $post = $this->post->set();

        $this->assertContainsOnlyInstancesOf('Themosis\PostType\PostType', array($post));

        // Add some custom post type arguments
        $post = $this->post->set(array(

            'supports'  => array('title', 'editor')

        ));

        $this->assertContainsOnlyInstancesOf('Themosis\PostType\PostType', array($post));
    }


    /**
     * testCanCreateACustomPostType function.
     *
     * @access public
     * @return void
     */
    public function testCanCreateACustomPostType()
    {
    	$post = $this->post->set();

    	// Insert a new post and make sure it's working.
    	$new = wp_insert_post(array(

    	    'post_title'    => 'A new book',
    	    'post_content'  => 'This is the content of the new book',
    	    'post_type'     => $post->getSlug()

    	));

    	$this->assertTrue(is_int($new));
    }

}

?>