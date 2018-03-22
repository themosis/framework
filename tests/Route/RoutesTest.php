<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Themosis\Route\Middleware\WordPressBindings;
use Themosis\Route\Router;

class RoutesTest extends TestCase
{
    public function testWordPressHomeRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('home', function () {
            return 'WordPress Home (blog archive)';
        });

        $router->post('home', function () {
            return 'Home post route';
        });

        $this->assertEquals(
            'WordPress Home (blog archive)',
            $router->dispatch(Request::create('/', 'GET'))->getContent()
        );

        $this->assertEquals(
            'Home post route',
            $router->dispatch(Request::create('/', 'POST'))->getContent()
        );
    }

    public function testWordPressBlogRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('blog', function () {
            return 'Another WordPress blog archive';
        });

        $this->assertEquals(
            'Another WordPress blog archive',
            $router->dispatch(Request::create('/', 'GET'))->getContent()
        );
    }

    public function testWordPress404Route()
    {
        $router = $this->getWordPressRouter();

        $router->get('404', function () {
            return 'Not found';
        });

        $this->assertEquals(
            'Not found',
            $router->dispatch(Request::create('asdfg', 'GET'))->getContent()
        );
    }

    public function testWordPressPageRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('page', function () {
            return 'Sample page';
        });

        $this->assertEquals(
            'Sample page',
            $router->dispatch(Request::create('sample-page', 'GET'))->getContent(),
            'Cannot reach default page.'
        );

        $router->get('page', [30, function () {
            return 'About page';
        }]);

        $this->assertEquals(
            'About page',
            $router->dispatch(Request::create('about', 'GET'))->getContent(),
            'Cannot reach the about page.'
        );

        $router->post('page', ['contact', function () {
            return 'Form submitted';
        }]);

        $this->assertEquals(
            'Form submitted',
            $router->dispatch(Request::create('contact', 'POST'))->getContent()
        );
    }

    public function testArchiveWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('archive', function () {
            return 'Blog posts';
        });

        $this->assertEquals(
            'Blog posts',
            $router->dispatch(Request::create('category', 'GET'))->getContent()
        );
    }

    public function testAttachmentWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('attachment', function () {
            return 'Some media';
        });

        $this->assertEquals(
            'Some media',
            $router->dispatch(Request::create('article/attachment/234', 'GET'))->getContent()
        );
    }

    public function testAuthorWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('author', function () {
            return 'Author posts';
        });

        $this->assertEquals(
            'Author posts',
            $router->dispatch(Request::create('author/john-doe', 'GET'))->getContent()
        );
    }

    public function testCategoryWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('category', function () {
            return 'General category';
        });

        $this->assertEquals(
            'General category',
            $router->dispatch(Request::create('category/uncategorized', 'GET'))->getContent()
        );

        $router->get('category', [20, function () {
            return 'Posts attached to category term with ID 20';
        }]);

        $this->assertEquals(
            'Posts attached to category term with ID 20',
            $router->dispatch(Request::create('category/special-20', 'GET'))->getContent()
        );

        $router->get('category', ['featured', function () {
            return 'Featured posts';
        }]);

        $this->assertEquals(
            'Featured posts',
            $router->dispatch(Request::create('category/featured', 'GET'))->getContent()
        );
    }

    public function testDateWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('date', function () {
            return 'Date archive';
        });

        $this->assertEquals(
            'Date archive',
            $router->dispatch(Request::create('2018', 'GET'))->getContent()
        );
    }

    public function testDayWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('day', function () {
            return 'Day archive';
        });

        $this->assertEquals(
            'Day archive',
            $router->dispatch(Request::create('2018/12/25', 'GET'))->getContent()
        );
    }

    public function testFrontPageWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('/', function () {
            return 'Front page';
        });

        $this->assertEquals(
            'Front page',
            $router->dispatch(Request::create('/', 'GET'))->getContent()
        );
    }

    public function testOtherFrontPageWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('front', function () {
            return 'Another front page';
        });

        $this->assertEquals(
            'Another front page',
            $router->dispatch(Request::create('/', 'GET'))->getContent()
        );
    }

    public function testMonthWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('month', function () {
            return 'Month archive';
        });

        $this->assertEquals(
            'Month archive',
            $router->dispatch(Request::create('2018/05', 'GET'))->getContent()
        );
    }

    public function testPagedWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('paged', function () {
            return 'Page 2 archive';
        });

        $this->assertEquals(
            'Page 2 archive',
            $router->dispatch(Request::create('category/featured/page/2', 'GET'))->getContent()
        );
    }

    public function testTemplateWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('template', function () {
            return 'Template page';
        });

        $this->assertEquals(
            'Template page',
            $router->dispatch(Request::create('any-page', 'GET'))->getContent()
        );

        $router->get('template', ['about', function () {
            return 'About page template';
        }]);

        $this->assertEquals(
            'About page template',
            $router->dispatch(Request::create('about', 'GET'))->getContent()
        );
    }

    public function testPostTypeArchiveWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('post-type-archive', function () {
            return 'Any post type archive';
        });

        $this->assertEquals(
            'Any post type archive',
            $router->dispatch(Request::create('some-slug', 'GET'))->getContent()
        );
    }

    public function testPostTypeArchiveWordPressRouteWithSlugParameter()
    {
        $router = $this->getWordPressRouter();

        $router->get('postTypeArchive', ['events', function () {
            return 'Events archive';
        }]);

        $this->assertEquals(
            'Events archive',
            $router->dispatch(Request::create('prefix-events', 'GET'))->getContent()
        );
    }

    public function testPostTypeArchiveWordPressRouteWithSlugParameterAsArray()
    {
        $router = $this->getWordPressRouter();

        $router->get('postTypeArchive', [['services', 'books'], function () {
            return 'Services or books archive';
        }]);

        $this->assertEquals(
            'Services or books archive',
            $router->dispatch(Request::create('prefix-services', 'GET'))->getContent()
        );
    }

    public function testSearchWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('search', function () {
            return 'Search results';
        });

        $this->assertEquals(
            'Search results',
            $router->dispatch(Request::create('?s=some-term', 'GET'))->getContent()
        );
    }

    public function testSingleWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('single', function () {
            return 'A post';
        });

        $this->assertEquals(
            'A post',
            $router->dispatch(Request::create('2025/04/12/a-hello-world-article', 'GET'))->getContent()
        );
    }

    public function testSingularWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('singular', function () {
            return 'A generic post';
        });

        $this->assertEquals(
            'A generic post',
            $router->dispatch(Request::create('2025/04/16/a-hello-world-article', 'GET'))->getContent()
        );
    }

    public function testSingularWordPressRouteWithSlug()
    {
        $router = $this->getWordPressRouter();

        $router->get('singular', ['books', function () {
            return 'A book post';
        }]);

        $this->assertEquals(
            'A book post',
            $router->dispatch(Request::create('books/big-book', 'GET'))->getContent()
        );
    }

    public function testSingularWordPressRouteWithArrayOfSlugs()
    {
        $router = $this->getWordPressRouter();

        $router->get('singular', [['events', 'services'], function () {
            return 'An event or service post';
        }]);

        $this->assertEquals(
            'An event or service post',
            $router->dispatch(Request::create('events/our-little-event', 'GET'))->getContent()
        );
    }

    public function testStickyWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('sticky', function () {
            return 'A sticky post';
        });

        $this->assertEquals(
            'A sticky post',
            $router->dispatch(Request::create('2345/02/14/valentine-day', 'GET'))->getContent()
        );
    }

    public function testTagWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('tag', function () {
            return 'Tag archive';
        });

        $this->assertEquals(
            'Tag archive',
            $router->dispatch(Request::create('tags/wordpress', 'GET'))->getContent()
        );
    }

    public function testTaxWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('tax', function () {
            return 'Tax archive';
        });

        $this->assertEquals(
            'Tax archive',
            $router->dispatch(Request::create('taxonomy/authors', 'GET'))->getContent()
        );
    }

    public function testTimeWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('time', function () {
            return 'Time archive';
        });

        $this->assertEquals(
            'Time archive',
            $router->dispatch(Request::create('2034/07/10/some-party-to-have', 'GET'))->getContent()
        );
    }

    public function testYearWordPressRoute()
    {
        $router = $this->getWordPressRouter();

        $router->get('year', function () {
            return 'Year archive';
        });

        $this->assertEquals(
            'Year archive',
            $router->dispatch(Request::create('2042', 'GET'))->getContent()
        );
    }

    public function testCustomCallbackRouteWithParameter()
    {
        $router = $this->getWordPressRouter();

        $router->get('custom', [42, function () {
            return 'Custom content';
        }]);

        $this->assertEquals(
            'Custom content',
            $router->dispatch(Request::create('whatever', 'GET'))->getContent()
        );

        $router->post('anything', [42, function () {
            return 'Post custom content';
        }]);

        $this->assertEquals(
            'Post custom content',
            $router->dispatch(Request::create('some-uri', 'POST'))->getContent()
        );
    }

    public function testRouteNotFoundIfNoCallback()
    {
        $router = $this->getWordPressRouter();

        $router->get('some-not-defined-callback-condition', function () {
            return 'Nothing';
        });

        $this->expectException(NotFoundHttpException::class);

        $router->dispatch(Request::create('some-random-uri', 'GET'));
    }

    public function testWordPressRouteParametersBindings()
    {
        $router = $this->getWordPressRouter();

        $route = $router->get('home', [
            'middleware' => WordPressBindings::class,
            'uses' => function () {
                return 'Something';
            }
        ]);

        $router->dispatch(Request::create('/', 'GET'));

        $this->assertEquals([
            'post' => null,
            'query' => null
        ], $route->parameters());
    }

    protected function getWordPressRouter()
    {
        $router = $this->getRouter();
        $router->setConditions([
            'is_404' => '404',
            'is_archive' => 'archive',
            'is_attachment' => 'attachment',
            'is_author' => 'author',
            'is_category' => ['category', 'cat'],
            'is_date' => 'date',
            'is_day' => 'day',
            'is_front_page' => ['/', 'front'],
            'is_home' => ['home', 'blog'],
            'is_month' => 'month',
            'is_page' => 'page',
            'is_paged' => 'paged',
            'is_page_template' => 'template',
            'is_post_type_archive' => ['post-type-archive', 'postTypeArchive'],
            'is_search' => 'search',
            'is_single' => 'single',
            'is_singular' => 'singular',
            'is_sticky' => 'sticky',
            'is_tag' => 'tag',
            'is_tax' => 'tax',
            'is_time' => 'time',
            'is_year' => 'year',
            'is_custom' => ['custom', 'anything']
        ]);

        return $router;
    }

    protected function getRouter()
    {
        $container = new Container();
        $router = new Router(new Dispatcher(), $container);
        $container->singleton(Registrar::class, function () use ($router) {
            return $router;
        });

        return $router;
    }
}
