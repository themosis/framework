<?php

namespace Themosis\Tests\Support;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Support\CallbackHandler;

class CallbackHandlerTest extends TestCase
{
    protected function getHandler()
    {
        return (new Handler())->setContainer(new Application());
    }

    public function testCallbackHandlerWithStringFunction()
    {
        $handler = $this->getHandler();

        $this->assertTrue($handler->render('themosis_callback_helper'));
    }

    public function testCallbackHandlerWithStringFunctionAndArguments()
    {
        $handler = $this->getHandler();

        $this->assertEquals('something', $handler->render('themosis_callback_helper', [
            'params' => 'something',
        ]));
    }

    public function testCallbackHandlerWithClosure()
    {
        $handler = $this->getHandler();

        $this->assertTrue($handler->render(function () {
            return true;
        }));

        $this->assertTrue($handler->render(function ($args) {
            return isset($args['key']);
        }, [
            'key' => 'value',
        ]));
    }

    public function testCallbackHandlerWithClassArrayType()
    {
        $handler = $this->getHandler();

        $this->assertTrue($handler->render([new CallbackClass(), 'index']));
        $this->assertEquals(42, $handler->render([new CallbackClass(), 'index'], [
            'count' => 42,
        ]));
    }

    public function testCallbackHandlerWithClassSyntax()
    {
        $handler = $this->getHandler();

        $this->assertTrue($handler->render('Themosis\Tests\Support\CallbackClass'));
        $this->assertEquals(42, $handler->render('Themosis\Tests\Support\CallbackClass', [
            'count' => 42,
        ]));

        $this->assertTrue($handler->render('Themosis\Tests\Support\CallbackClass@custom'));
        $this->assertEquals('hello', $handler->render('Themosis\Tests\Support\CallbackClass@custom', ['var' => 'hello']));
    }
}

class Handler
{
    use CallbackHandler;

    public function render($callback, array $args = [])
    {
        return $this->handleCallback($callback, $args);
    }
}

class CallbackClass
{
    public function index($args = [])
    {
        if (isset($args['count'])) {
            return $args['count'];
        }

        return true;
    }

    public function custom($args)
    {
        if (isset($args['var'])) {
            return $args['var'];
        }

        return true;
    }
}
