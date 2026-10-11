<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
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
            $router->dispatch(Request::create('/', 'GET'))->getContent(),
        );

        $this->assertEquals(
            'Home post route',
            $router->dispatch(Request::create('/', 'POST'))->getContent(),
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
            $router->dispatch(Request::create('/', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('asdfg', 'GET'))->getContent(),
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
            'Cannot reach default page.',
        );

        $router->post('page', ['contact', function () {
            return 'Form submitted';
        }]);

        $this->assertEquals(
            'Form submitted',
            $router->dispatch(Request::create('contact', 'POST'))->getContent(),
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
            $router->dispatch(Request::create('category', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('article/attachment/234', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('author/john-doe', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('category/uncategorized', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2018', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2018/12/25', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('/', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('/', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2018/05', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('category/featured/page/2', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('any-page', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('some-slug', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('prefix-events', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('prefix-services', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('?s=some-term', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2025/04/12/a-hello-world-article', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2025/04/16/a-hello-world-article', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('books/big-book', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('events/our-little-event', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2345/02/14/valentine-day', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('tags/wordpress', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('taxonomy/authors', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2034/07/10/some-party-to-have', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('2042', 'GET'))->getContent(),
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
            $router->dispatch(Request::create('whatever', 'GET'))->getContent(),
        );

        $router->post('anything', [42, function () {
            return 'Post custom content';
        }]);

        $this->assertEquals(
            'Post custom content',
            $router->dispatch(Request::create('some-uri', 'POST'))->getContent(),
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
            },
        ]);

        $router->dispatch(Request::create('/', 'GET'));

        $this->assertEquals([
            'post' => null,
            'wp_query' => null,
        ], $route->parameters());
    }

    public function testWordPressRouteWithController()
    {
        $router = $this->getWordPressRouter();

        $route = $router->get('home', 'FooController@index')
            ->middleware(WordPressBindings::class);

        $this->assertEquals(
            'Controller index action',
            $router->dispatch(Request::create('/', 'GET'))->getContent(),
        );

        $this->assertEquals([
            'post' => null,
            'wp_query' => null,
        ], $route->parameters());
    }

    /**
     * @see laravel/framework/tests/Routing/RoutingRouteTest.php
     */
    public function testBasicRouting()
    {
        $router = $this->getWordPressRouter();
        $router->get('foo/bar', function () {
            return 'hello';
        });
        $this->assertEquals('hello', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('foo/bar', function () {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(new Response('hello'));
        });
        $this->assertEquals('hello', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('foo/bar', ['domain' => 'api.{name}.bar', function ($name) {
            return $name;
        }]);
        $router->get('foo/bar', ['domain' => 'api.{name}.baz', function ($name) {
            return $name;
        }]);
        $this->assertEquals(
            'themosis',
            $router->dispatch(Request::create('http://api.themosis.bar/foo/bar', 'GET'))->getContent(),
        );
        $this->assertEquals(
            'wordpress',
            $router->dispatch(Request::create('http://api.wordpress.baz/foo/bar', 'GET'))->getContent(),
        );

        $router = $this->getWordPressRouter();
        $router->get('foo/{age}', ['domain' => 'api.{name}.bar', function ($name, $age) {
            return $name . $age;
        }]);
        $this->assertEquals(
            'max35',
            $router->dispatch(
                Request::create('http://api.max.bar/foo/35', 'GET'),
            )->getContent(),
        );

        $router = $this->getWordPressRouter();
        $router->get('foo/bar', function () {
            return 'hello';
        });
        $router->post('foo/bar', function () {
            return 'post hello';
        });
        $this->assertEquals('hello', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());
        $this->assertEquals('post hello', $router->dispatch(Request::create('foo/bar', 'POST'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('foo/{bar}', function ($name) {
            return $name;
        });
        $this->assertEquals('john', $router->dispatch(Request::create('foo/john', 'GET'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('foo/{name}/boom/{age?}/{location?}', function ($name, $age = 25, $location = 'AR') {
            return $name . $age . $location;
        });
        $this->assertEquals(
            'wordpress30AR',
            $router->dispatch(Request::create('foo/wordpress/boom/30', 'GET'))->getContent(),
        );

        $router = $this->getWordPressRouter();
        $router->get('{bar}/{baz?}', function ($name, $age = 25) {
            return $name . $age;
        });
        $this->assertEquals('wordpress25', $router->dispatch(Request::create('wordpress', 'GET'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('{baz?}', function ($age = 25) {
            return $age;
        });
        $this->assertEquals('25', $router->dispatch(Request::create('/', 'GET'))->getContent());
        $this->assertEquals('30', $router->dispatch(Request::create('30', 'GET'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('{foo?}/{baz?}', ['as' => 'foo', function ($name = 'julien', $age = 25) {
            return $name . $age;
        }]);
        $this->assertEquals('julien25', $router->dispatch(Request::create('/', 'GET'))->getContent());
        $this->assertEquals('marcel25', $router->dispatch(Request::create('marcel', 'GET'))->getContent());
        $this->assertEquals('marcel30', $router->dispatch(Request::create('marcel/30', 'GET'))->getContent());
        $this->assertTrue($router->currentRouteNamed('foo'));
        $this->assertTrue($router->currentRouteNamed('fo*'));
        $this->assertTrue($router->is('foo'));
        $this->assertTrue($router->is('foo', 'bar'));
        $this->assertFalse($router->is('bar'));

        $router = $this->getWordPressRouter();
        $router->get('foo/{file}', function ($file) {
            return $file;
        });
        $this->assertEquals(
            'oxygen%20',
            $router->dispatch(Request::create('http://test.com/foo/oxygen%2520', 'GET'))->getContent(),
        );

        $router = $this->getWordPressRouter();
        $router->patch('foo/bar', ['as' => 'foo', function () {
            return 'bar';
        }]);

        $this->assertEquals('bar', $router->dispatch(Request::create('foo/bar', 'PATCH'))->getContent());
        $this->assertEquals('foo', $router->currentRouteName());

        $router = $this->getWordPressRouter();
        $router->get('foo/bar', function () {
            return 'hello';
        });
        $this->assertEmpty($router->dispatch(Request::create('foo/bar', 'HEAD'))->getContent());

        $router = $this->getWordPressRouter();
        $router->any('foo/bar', function () {
            return 'hello';
        });
        $this->assertEmpty($router->dispatch(Request::create('foo/bar', 'HEAD'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('foo/bar', function () {
            return 'first';
        });
        $router->get('foo/bar', function () {
            return 'second';
        });
        $this->assertEquals('second', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());

        $router = $this->getWordPressRouter();
        $router->get('foo/bar/åαф', function () {
            return 'hello';
        });
        $this->assertEquals(
            'hello',
            $router->dispatch(Request::create('foo/bar/%C3%A5%CE%B1%D1%84', 'GET'))->getContent(),
        );

        $router = $this->getWordPressRouter();
        $router->get('foo/bar', ['boom' => 'auth', function () {
            return 'closure';
        }]);
        $this->assertEquals('closure', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());
    }

    public function testNotModifiedResponseIsProperlyReturned()
    {
        $router = $this->getWordPressRouter();
        $router->get('test', function () {
            return (new SymfonyResponse('test', 304, ['foo' => 'bar']))->setLastModified(new DateTime());
        });
        $response = $router->dispatch(Request::create('test', 'GET'));
        $this->assertSame(304, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
        $this->assertSame('bar', $response->headers->get('foo'));
        $this->assertNull($response->getLastModified());
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
            'is_custom' => ['custom', 'anything'],
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
        
        // Add CallableDispatcher binding
        $container->singleton('Illuminate\Routing\Contracts\CallableDispatcher', function () use ($container) {
            return new \Illuminate\Routing\CallableDispatcher($container);
        });

        return $router;
    }
}

class FooController extends Controller
{
    public function index()
    {
        return 'Controller index action';
    }
}
