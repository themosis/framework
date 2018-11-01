<?php

namespace Themosis\Tests;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Taxonomy\Factory;

class TaxonomyTest extends TestCase
{
    protected $application;

    protected function getApplication()
    {
        return (new Application());
    }

    protected function getFactory()
    {
        return new Factory($this->getApplication());
    }

    public function testCreateTaxonomyWithDefaults()
    {
        $factory = $this->getFactory();

        $taxonomy = $factory->make('author', 'post', 'Authors', 'Author');

        $this->assertEquals('Popular Authors', $taxonomy->getLabel('popular_items'));
        $this->assertEmpty($taxonomy->getLabel('not_defined'));
    }
}
