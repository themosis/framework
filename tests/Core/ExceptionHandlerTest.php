<?php

namespace Themosis\Tests\Core;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
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

    public function setUp(): void
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
                    $redirector,
                );
            },
        );

        Facade::setFacadeApplication($this->container);

        $this->handler = new Handler($this->container);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        parent::tearDown();
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
            ->will($this->returnValue(true));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

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
            ->will($this->returnValue(false));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

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
            ->will($this->returnValue(false));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

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
            ->will($this->returnValue(false));
        $this->request->expects($this->once())->method('expectsJson')->will($this->returnValue(true));

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
        $file->method('getError')->willReturn(UPLOAD_ERR_OK);

        $request = Request::create('/', 'POST', $argumentExpected, [], ['photo' => $file]);

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new MessageBag(['error' => 'My custom validation exception']));

        $validationException = new ValidationException($validator);
        $validationException->redirectTo = '/';

        $this->handler->render($request, $validationException);

        $this->assertEquals($argumentExpected, $argumentActual);
    }

    /*
    |--------------------------------------------------------------------------
    | prepareException mapping
    |--------------------------------------------------------------------------
    */

    /**
     * @dataProvider exceptionMappingProvider
     */
    public function testPrepareExceptionMapsToExpectedHttpStatus(
        \Throwable $exception,
        int $expectedStatus,
    ): void {
        $this->config->method('get')->willReturn(false);
        $this->request->method('expectsJson')->willReturn(true);

        $response = $this->handler->render($this->request, $exception);

        $this->assertSame($expectedStatus, $response->getStatusCode());
    }

    public static function exceptionMappingProvider(): array
    {
        return [
            'ModelNotFoundException maps to 404' => [new ModelNotFoundException(), 404],
            'AuthorizationException maps to 403' => [new AuthorizationException(), 403],
            'TokenMismatchException maps to 419' => [new TokenMismatchException(), 419],
            'SuspiciousOperationException maps to 404' => [new SuspiciousOperationException(), 404],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | unauthenticated
    |--------------------------------------------------------------------------
    */

    public function testUnauthenticatedWithJsonRequestReturns401(): void
    {
        $this->request->method('expectsJson')->willReturn(true);

        $response = $this->handler->render(
            $this->request,
            new AuthenticationException('Unauthenticated.'),
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(401, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame('Unauthenticated.', $content['message']);
    }

    public function testUnauthenticatedWithNonJsonRequestRedirectsToGuestUrl(): void
    {
        $this->request->method('expectsJson')->willReturn(false);

        $redirectResponse = $this->createMock(RedirectResponse::class);
        $redirector = $this->createMock(Redirector::class);
        $redirector->expects($this->once())
            ->method('guest')
            ->with('/login')
            ->willReturn($redirectResponse);
        $this->container->instance('redirect', $redirector);

        $response = $this->handler->render(
            $this->request,
            new AuthenticationException('Unauthenticated.', [], '/login'),
        );

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    /*
    |--------------------------------------------------------------------------
    | invalidJson
    |--------------------------------------------------------------------------
    */

    public function testValidationExceptionWithJsonRequestReturns422WithErrorsAndMessage(): void
    {
        $this->request->method('expectsJson')->willReturn(true);

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new MessageBag(['name' => ['Required.']]));

        $response = $this->handler->render(
            $this->request,
            new ValidationException($validator),
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(422, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('errors', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Registration API — renderable / reportable / map / ignore
    |--------------------------------------------------------------------------
    */

    public function testRenderableCallbackIsHonoredDuringRender(): void
    {
        $called = false;
        $this->handler->renderable(function (\RuntimeException $e, $request) use (&$called) {
            $called = true;

            return new JsonResponse(['custom' => true]);
        });

        $response = $this->handler->render($this->request, new \RuntimeException('test'));

        $this->assertTrue($called);
        $content = json_decode($response->getContent(), true);
        $this->assertSame(true, $content['custom']);
    }

    public function testReportableCallbackFiresOnReport(): void
    {
        $fired = false;
        $this->handler->reportable(function (\RuntimeException $e) use (&$fired) {
            $fired = true;
        });

        $logger = $this->createMock(LoggerInterface::class);
        $this->container->instance(LoggerInterface::class, $logger);

        $this->handler->report(new \RuntimeException('test'));

        $this->assertTrue($fired);
    }

    public function testMapConvertsExceptionTypeForRendering(): void
    {
        $this->config->method('get')->willReturn(false);
        $this->request->method('expectsJson')->willReturn(true);

        $this->handler->map(\RuntimeException::class, ModelNotFoundException::class);

        $response = $this->handler->render($this->request, new \RuntimeException('test'));

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testIgnoredExceptionIsNotReported(): void
    {
        $handler = new HandlerThatIgnoresRuntimeException($this->container);

        $this->assertFalse($handler->shouldReport(new \RuntimeException('test')));
    }

    /*
    |--------------------------------------------------------------------------
    | Non-JSON prepareResponse
    |--------------------------------------------------------------------------
    */

    public function testNonJsonRequestReturnsSymfonyResponseForGenericException(): void
    {
        $this->config->method('get')
            ->willReturnMap([
                ['app.debug', null, false],
                ['view.paths', null, null],
            ]);
        $this->request->method('expectsJson')->willReturn(false);

        $viewFactory = $this->getMockBuilder(Factory::class)->getMock();
        $viewFactory->method('exists')->willReturn(false);
        $this->container->instance('view', $viewFactory);
        $this->container->instance(Factory::class, $viewFactory);

        $response = $this->handler->render($this->request, new \RuntimeException('test'));

        $this->assertInstanceOf(SymfonyResponse::class, $response);
        $this->assertSame(500, $response->getStatusCode());
    }
}

class HandlerThatIgnoresRuntimeException extends Handler
{
    public function register()
    {
        $this->ignore(\RuntimeException::class);
    }
}

class CustomException extends \Exception implements Responsable
{
    public function toResponse($request)
    {
        return response()->json(['response' => 'Custom exception response']);
    }
}
