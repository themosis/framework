<?php

namespace Themosis\Core\Bootstrap;

use ErrorException;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\LogManager;
use Monolog\Handler\NullHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\ErrorHandler\Error\FatalError;

class ExceptionHandler
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Reserved memory so that errors can be displayed properly on memory exhaustion.
     *
     * @var string
     */
    public static $reservedMemory;

    public function bootstrap(Application $app)
    {
        self::$reservedMemory = str_repeat('x', 10240);

        $this->app = $app;

        error_reporting(-1);

        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);

        register_shutdown_function([$this, 'handleShutdown']);

        if (! $app->environment('testing')) {
            ini_set('display_errors', 'Off');
        }
    }

    /**
     * Convert PHP errors to ErrorException instances.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     *
     * @throws ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if ($this->isDeprecation($level)) {
            return $this->handleDeprecation($message, $file, $line);
        }

        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Reports a deprecation to the "deprecations" logger.
     *
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     */
    public function handleDeprecation($message, $file, $line)
    {
        if (! class_exists(LogManager::class)
        || ! $this->app->hasBeenBootstrapped()
        || $this->app->runningUnitTests()) {
            return;
        }

        try {
            $logger = $this->app->make(LogManager::class);
        } catch (Exception $e) {
            return;
        }

        $this->ensureDeprecationLoggerIsConfigured();

        with($logger->channel('deprecations'), function ($log) use ($message, $file, $line) {
            $log->warning(sprintf(
                '%s in %s on line %s',
                $message,
                $file,
                $line,
            ));
        });
    }

    protected function ensureDeprecationLoggerIsConfigured()
    {
        with($this->app['config'], function ($config) {
            if ($config->get('logging.channels.deprecations')) {
                return;
            }

            $this->ensureNullLogDriverIsConfigured();

            $driver = $config->get('logging.deprecations') ?? 'null';

            $config->set('logging.channels.deprecations', $config->get("logging.channels.{$driver}"));
        });
    }

    protected function ensureNullLogDriverIsConfigured()
    {
        with($this->app['config'], function ($config) {
            if ($config->get('logging.channels.null')) {
                return;
            }

            $config->set('logging.channels.null', [
                'driver' => 'monolog',
                'handler' => NullHandler::class,
            ]);
        });
    }

    /**
     * Handle an uncaught exception from the application.
     */
    public function handleException(\Throwable $e)
    {
        try {
            self::$reservedMemory = null;

            $this->getExceptionHandler()->report($e);
        } catch (Exception $e) {
            //
        }

        if ($this->app->runningInConsole()) {
            $this->renderForConsole($e);
        } else {
            $this->renderHttpResponse($e);
        }
    }

    /**
     * Handle the PHP shutdown event.
     */
    public function handleShutdown()
    {
        if (! is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }

    /**
     * Determine if the error level is a deprecation.
     *
     * @param  int  $level
     * @return bool
     */
    protected function isDeprecation($level)
    {
        return in_array($level, [E_DEPRECATED, E_USER_DEPRECATED]);
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param  int  $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

    /**
     * Create a new fatal exception instance from an error array.
     *
     * @param  int|null  $traceOffset
     * @return \Symfony\Component\ErrorHandler\Error\FatalError
     */
    protected function fatalExceptionFromError(array $error, $traceOffset = null)
    {
        return new FatalError(
            $error['message'],
            0,
            $error,
            $traceOffset,
        );
    }

    /**
     * Render an exception to the console.
     */
    protected function renderForConsole(\Throwable $e)
    {
        $this->getExceptionHandler()->renderForConsole(new ConsoleOutput(), $e);
    }

    /**
     * Render an exception as an HTTP Response and send it.
     */
    protected function renderHttpResponse(\Throwable $e)
    {
        $this->getExceptionHandler()->render($this->app['request'], $e)->send();
    }

    /**
     * Get an instance of the exception handler.
     *
     * @return \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected function getExceptionHandler()
    {
        return $this->app->make(ExceptionHandlerContract::class);
    }
}
