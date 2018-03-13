<?php

use PHPUnit\Framework\TestCase;
use Themosis\Hook\FilterBuilder;

class FilterTest extends TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new \Themosis\Foundation\Application();
    }

    public function testFilterWithClosure()
    {
        $filter = new FilterBuilder($this->app);

        $filter->add('filter-one', function () {
        });

        // Check if filter is registered.
        $this->assertTrue($filter->exists('filter-one'));

        // Check attached callback is a Closure.
        $this->assertInstanceOf('\Closure', $filter->getCallback('filter-one')[0]);

        // Check default priority.
        $this->assertEquals(10, $filter->getCallback('filter-one')[1]);

        // Check default accepted_args.
        $this->assertEquals(3, $filter->getCallback('filter-one')[2]);
    }

    public function testFilterWithClass()
    {
        $filter = new FilterBuilder($this->app);

        $filter->add('custom-filter', 'AFilterClassForTest', 4, 2);

        // Check if this filter is registered.
        $this->assertTrue($filter->exists('custom-filter'));

        // Check the attached callback is an array with instance of AFilterClassForTest.
        // In this test, we also test the hyphen are converted into an underscore
        // for language compatibility.
        $class = new AFilterClassForTest();
        $callback = $filter->getCallback('custom-filter')[0]; // array [instance, 'method']

        // Check if method name has been converted with an underscore.
        $this->assertEquals('custom_filter', $callback[1]);

        // Check callback is defined and with method name converted.
        $this->assertEquals([$class, 'custom_filter'], $filter->getCallback('custom-filter')[0]);

        // Check defined priority.
        $this->assertEquals(4, $filter->getCallback('custom-filter')[1]);

        // Check defined accepted_args.
        $this->assertEquals(2, $filter->getCallback('custom-filter')[2]);

        // Run filter with pre-defined method name.
        $filter->add('another-filter', 'AFilterClassForTest@awesomeFilter');

        // Check this filter is registered.
        $this->assertTrue($filter->exists('another-filter'));

        // Check attached callback is an array with instance of AFilterClassForTest and a method of customFilter.
        $this->assertEquals([$class, 'awesomeFilter'], $filter->getCallback('another-filter')[0]);
    }

    public function testFilterWithNamedCallback()
    {
        $filter = new FilterBuilder($this->app);

        $filter->add('uncharted', 'callingForUncharted');

        // Check if this action is registered.
        $this->assertTrue($filter->exists('uncharted'));

        // Check if callback is callable.
        $this->assertTrue(is_callable($filter->getCallback('uncharted')[0]));
    }
}
