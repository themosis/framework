<?php

namespace Themosis\Tests;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Hook\ActionBuilder;
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
        $app = $this->getApplication();

        return new Factory($app, new ActionBuilder($app));
    }

    public function testCreateTaxonomyWithDefaults()
    {
        $factory = $this->getFactory();

        $taxonomy = $factory->make('author', 'post', 'Authors', 'Author');

        $this->assertEquals('Popular Authors', $taxonomy->getLabel('popular_items'));
        $this->assertEmpty($taxonomy->getLabel('not_defined'));
        $this->assertTrue($taxonomy->getArgument('public'));
        $this->assertTrue($taxonomy->getArgument('show_in_rest'));

        $taxonomy->setObjects('page');

        $this->assertEquals(['post', 'page'], $taxonomy->getObjects());
    }

    public function testCreateCustomTaxonomy()
    {
        $factory = $this->getFactory();

        $taxonomy = $factory->make('theme', ['projects'], 'Themes', 'Theme')
            ->setLabels([
                'popular_items' => 'Best Themes'
            ])
            ->setObjects(['post', 'wireframes'])
            ->setArguments([
                'show_in_rest' => false,
                'public' => false
            ]);

        $this->assertEquals('Best Themes', $taxonomy->getLabel('popular_items'));
        $this->assertEquals(['projects', 'post', 'wireframes'], $taxonomy->getObjects());
    }
}
