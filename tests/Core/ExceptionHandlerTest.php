<?php

namespace Themosis\Tests\Core;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Themosis\Core\Exceptions\Handler;

class TestRequest
{
    public function expectsJson()
    {
        return true;
    }
}

class ExceptionHandlerTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Handler
     */
    protected $handler;

    protected $request;

    /**
     * @var Repository
     */
    protected $config;

    public function setUp(): void
    {
        $this->container = Container::setInstance(new Container());

        $this->request = new TestRequest();

        $this->config = $this->createMock(Repository::class);
        $this->container->singleton('config', function () {
            return $this->config;
        });

        $viewFactory = $this->createMock(Factory::class);
        $redirector = $this->createMock(Redirector::class);

        $this->container->singleton(
            'Illuminate\Contracts\Routing\ResponseFactory',
            function () use ($viewFactory, $redirector) {
                return new ResponseFactory(
                    $viewFactory,
                    $redirector,
                );
            },
        );

        $this->handler = new Handler($this->container);
    }

    public function testHandlerReportExceptionAsContext()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->container->instance(LoggerInterface::class, $logger);

        $exception = new \RuntimeException('Exception message');

        $logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('Exception message'),
                [
                    'exception' => $exception,
                ],
            );

        $this->handler->report($exception);
    }

    public function testReturnsJsonWithStackTraceWhenAjaxRequestAndDebugTrue()
    {
        $this->config->expects($this->once())
            ->method('get')
            ->with('app.debug', $this->equalTo(null))
            ->willReturn(true);

        $response = $this->handler->render(
            $this->request,
            new \Exception('Custom error message'),
        )->getContent();

        $this->assertFalse(strpos($response, '<!DOCTYPE html>'));
        $this->assertTrue(false !== strpos($response, '"message": "Custom error message"'));
        $this->assertTrue(false !== strpos($response, '"file":'));
        $this->assertTrue(false !== strpos($response, '"line":'));
        $this->assertTrue(false !== strpos($response, '"trace":'));
    }

    public function testReturnsCustomResponseWhenExceptionImplementResponsable()
    {
        $response = $this->handler->render($this->request, new CustomException())->getContent();

        $this->assertSame('{"response":"Custom exception response"}', $response);
    }

    public function testReturnsJsonWithoutStackTraceWhenAjaxRequestAndDebugFalseAndExceptionMessageIsMasked()
    {
        $this->config->expects($this->once())
            ->method('get')
            ->with('app.debug', $this->equalTo(null))
            ->willReturn(false);

        $response = $this->handler->render(
            $this->request,
            new \Exception('This error message should not be visible'),
        )->getContent();

        $this->assertTrue(false !== strpos($response, '"message": "Server Error"'));

        $this->assertFalse(strpos($response, '<!DOCTYPE html>'));
        $this->assertFalse(strpos($response, 'This error message should not be visible'));
        $this->assertFalse(strpos($response, '"file":'));
        $this->assertFalse(strpos($response, '"line":'));
        $this->assertFalse(strpos($response, '"trace":'));
    }

    public function testReturnsJsonWithoutStackTraceWhenAjaxRequestAndDebugFalseAndHttpExceptionIsShown()
    {
        $this->config->expects($this->once())
            ->method('get')
            ->with('app.debug', $this->equalTo(null))
            ->willReturn(false);

        $response = $this->handler->render(
            $this->request,
            new HttpException(
                403,
                'Custom error message',
            ),
        )->getContent();

        $this->assertTrue(false !== strpos($response, '"message": "Custom error message"'));
        $this->assertFalse(strpos($response, '<!DOCTYPE html>'));
        $this->assertFalse(strpos($response, '"message": "Server Error"'));
        $this->assertFalse(strpos($response, '"file":'));
        $this->assertFalse(strpos($response, '"line":'));
        $this->assertFalse(strpos($response, '"trace":'));
    }

    public function testReturnsJsonWithoutStackTraceWhenAjaxRequestAndDebugFalseAndAccessDeniedHttpExceptionErrorIsShown()
    {
        $this->config->expects($this->once())
            ->method('get')
            ->with('app.debug', $this->equalTo(null))
            ->willReturn(false);

        $response = $this->handler->render(
            $this->request,
            new AccessDeniedHttpException('Custom error message'),
        )->getContent();

        $this->assertTrue(false !== strpos($response, '"message": "Custom error message"'));
        $this->assertFalse(strpos($response, '<!DOCTYPE html>'));
        $this->assertFalse(strpos($response, '"message": "Server Error"'));
        $this->assertFalse(strpos($response, '"file":'));
        $this->assertFalse(strpos($response, '"line":'));
        $this->assertFalse(strpos($response, '"trace":'));
    }

    public function testValidateFileMethod()
    {
        $argumentExpected = ['input' => 'My input value'];
        $argumentActual = null;

        $this->container->singleton('redirect', function () use (&$argumentActual) {
            $redirector = $this->createMock(Redirector::class);

            $redirector->expects($this->once())
                ->method('to')
                ->willReturn($responser = $this->createMock(RedirectResponse::class));

            $responser->expects($this->once())
                ->method('withInput')
                ->with($this->callback(function ($argument) use (&$argumentActual) {
                    $argumentActual = $argument;

                    return true;
                }))
                ->willReturn($responser);

            $responser->expects($this->once())
                ->method('withErrors')
                ->willReturn($responser);

            return $redirector;
        });

        $file = $this->createMock(UploadedFile::class);
        $file->method('getPathname')->willReturn('photo.jpg');
        $file->method('getClientOriginalName')->willReturn('photo.jpg');
        $file->method('getClientMimeType')->willReturn('image/jpeg');
        $file->method('getError')->willReturn(0);

        $request = Request::create('/', 'POST', $argumentExpected, [], ['photo' => $file]);

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new MessageBag(['error' => 'My custom validation exception']));

        $validationException = new ValidationException($validator);
        $validationException->redirectTo = '/';

        $this->handler->render($request, $validationException);

        $this->assertEquals($argumentExpected, $argumentActual);
    }
}

class CustomException extends \Exception implements Responsable
{
    public function toResponse($request)
    {
        return response()->json(['response' => 'Custom exception response']);
    }
}
