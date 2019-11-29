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

    public function setUp()
    {
        $this->container = Container::setInstance(new Container());

        $this->request = $this->getMockBuilder('stdClass')
            ->setMethods(['expectsJson'])
            ->getMock();

        $this->config = $config = $this->getMockBuilder(Repository::class)
            ->setMethods(['get'])
            ->getMock();
        $this->container->singleton('config', function () use ($config) {
            return $config;
        });

        $viewFactory = $this->getMockBuilder(Factory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redirector = $this->getMockBuilder(Redirector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->singleton(
            'Illuminate\Contracts\Routing\ResponseFactory',
            function () use ($viewFactory, $redirector) {
                return new ResponseFactory(
                    $viewFactory,
                    $redirector
                );
            }
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
                    'exception' => $exception
                ]
            );

        $this->handler->report($exception);
    }

    public function testReturnsJsonWithStackTraceWhenAjaxRequestAndDebugTrue()
    {
        $this->config->expects($this->once())
            ->method('get')
            ->with('app.debug', $this->equalTo(null))
            ->will($this->returnValue(true));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

        $response = $this->handler->render(
            $this->request,
            new \Exception('Custom error message')
        )->getContent();

        $this->assertNotContains('<!DOCTYPE html>', $response);
        $this->assertContains('"message": "Custom error message"', $response);
        $this->assertContains('"file":', $response);
        $this->assertContains('"line":', $response);
        $this->assertContains('"trace":', $response);
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
            ->will($this->returnValue(false));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

        $response = $this->handler->render(
            $this->request,
            new \Exception('This error message should not be visible')
        )->getContent();

        $this->assertContains('"message": "Server Error"', $response);
        $this->assertNotContains('<!DOCTYPE html>', $response);
        $this->assertNotContains('This error message should not be visible', $response);
        $this->assertNotContains('"file":', $response);
        $this->assertNotContains('"line":', $response);
        $this->assertNotContains('"trace":', $response);
    }

    public function testReturnsJsonWithoutStackTraceWhenAjaxRequestAndDebugFalseAndHttpExceptionIsShown()
    {
        $this->config->expects($this->once())
            ->method('get')
            ->with('app.debug', $this->equalTo(null))
            ->will($this->returnValue(false));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

        $response = $this->handler->render(
            $this->request,
            new HttpException(
                403,
                'Custom error message'
            )
        )->getContent();

        $this->assertContains('"message": "Custom error message"', $response);
        $this->assertNotContains('<!DOCTYPE html>', $response);
        $this->assertNotContains('"message": "Server Error"', $response);
        $this->assertNotContains('"file":', $response);
        $this->assertNotContains('"line":', $response);
        $this->assertNotContains('"trace":', $response);
    }

    public function testReturnsJsonWithoutStackTraceWhenAjaxRequestAndDebugFalseAndAccessDeniedHttpExceptionErrorIsShown()
    {
        $this->config->expects($this->once())
            ->method('get')
            ->with('app.debug', $this->equalTo(null))
            ->will($this->returnValue(false));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

        $response = $this->handler->render(
            $this->request,
            new AccessDeniedHttpException('Custom error message')
        )->getContent();

        $this->assertContains('"message": "Custom error message"', $response);
        $this->assertNotContains('<!DOCTYPE html>', $response);
        $this->assertNotContains('"message": "Server Error"', $response);
        $this->assertNotContains('"file":', $response);
        $this->assertNotContains('"line":', $response);
        $this->assertNotContains('"trace":', $response);
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
        $file->method('getClientMimeType')->willReturn(null);
        $file->method('getError')->willReturn(null);

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
