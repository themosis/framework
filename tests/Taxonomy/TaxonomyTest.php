<?php

namespace Themosis\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\TestCase;
use Themosis\Hook\ActionBuilder;
use Themosis\Taxonomy\Factory;
use Themosis\Taxonomy\TaxonomyField;
use Themosis\Taxonomy\TaxonomyFieldRepository;

class TaxonomyTest extends TestCase
{
    use Application;
    use ViewFactory;

    protected function getFactory()
    {
        $app = $this->getApplication();

        return new Factory($app, new ActionBuilder($app));
    }

    protected function getFieldsFactory()
    {
        $app = $this->getApplication();

        return new \Themosis\Field\Factory(
            $app,
            $this->getViewFactory($app, [
                __DIR__.'/../../../framework/src/Forms/views',
            ]),
        );
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
                'popular_items' => 'Best Themes',
            ])
            ->setObjects(['post', 'wireframes'])
            ->setArguments([
                'show_in_rest' => false,
                'public' => false,
            ]);

        $this->assertEquals('Best Themes', $taxonomy->getLabel('popular_items'));
        $this->assertEquals(['projects', 'post', 'wireframes'], $taxonomy->getObjects());
    }

    public function testTaxonomyRepositoryCanAddFields()
    {
        $repository = new TaxonomyFieldRepository();
        $fields = $this->getFieldsFactory();

        $repository->add($note = $fields->text('note'));

        $repository->add([
            $notice = $fields->text('notice'),
            $email = $fields->email('email'),
        ]);

        $this->assertEquals($note, $repository->getFieldByName('note'));

        $this->assertEquals($notice, $repository->getFieldByName('notice'));
        $this->assertEquals($email, $repository->getFieldByName('email'));
    }

    public function testTaxonomyField()
    {
        $factory = $this->getFactory();
        $fields = $this->getFieldsFactory();

        $taxonomy = $factory->make('category', 'post', 'Categories', 'Category');

        $taxonomyField = new TaxonomyField(
            $taxonomy,
            $repository = new TaxonomyFieldRepository(),
            $this->getViewFactory($this->getApplication()),
            new \Illuminate\Validation\Factory(new Translator(new FileLoader(new Filesystem(), ''), 'en_US')),
            new ActionBuilder($this->getApplication()),
            ['theme' => 'themosis.taxonomy', 'prefix' => 'wp_'],
        );

        $taxonomyField->add($fields->text('note'));

        $this->assertEquals('themosis.taxonomy', $repository->getFieldByName('note')->getTheme());
        $this->assertEquals('wp_', $repository->getFieldByName('note')->getPrefix());
    }
}
