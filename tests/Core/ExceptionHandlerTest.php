<?php

namespace Themosis\Tests\Core;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
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
                    'exception' => $exception,
                    'userId' => null,
                    'email' => null
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
}
