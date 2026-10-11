<?php

namespace Themosis\Core\Bootstrap;

use ErrorException;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\LogManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Throwable;

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
     * @param int    $level
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     *
     * @throws ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        // Deprecations are logged regardless of the error_reporting() mask (as Laravel does),
        // so a WordPress or mu-plugin mask keeps them off-screen while they still reach the log.
        if ($this->isDeprecation($level)) {
            try {
                $this->handleDeprecation($message, $file, $line, $level);
            } catch (Throwable $e) {
                // Never let deprecation handling block execution.
            }

            return true;
        }

        if (! (error_reporting() & $level)) {
            return;
        }

        // Check if this is a warning or notice that shouldn't stop execution
        if ($this->isWarning($level)) {
            try {
                // Log the warning if the app is bootstrapped
                if ($this->app->hasBeenBootstrapped() && $this->app->bound('log')) {
                    $this->app->make('log')->warning(sprintf(
                        'PHP %s: %s in %s on line %s',
                        $this->getErrorLevelString($level),
                        $message,
                        $file,
                        $line
                    ));
                }
                // Don't throw exception for warnings - allow execution to continue
                return;
            } catch (Exception $e) {
                // If logging fails, continue without blocking
                return;
            }
        }

        // Only throw exceptions for fatal/serious errors
        if ($this->shouldThrowException($level)) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Reports a deprecation to the configured deprecations log channel.
     *
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param int    $level
     */
    public function handleDeprecation($message, $file, $line, $level = E_DEPRECATED)
    {
        if ($this->shouldIgnoreDeprecationErrors()) {
            return;
        }

        $options = $this->deprecationLoggingOptions();

        if (is_null($channel = $this->resolveDeprecationChannel($options['channel']))) {
            return;
        }

        try {
            $logger = $this->app->make(LogManager::class)->channel($channel);
        } catch (Throwable $e) {
            return;
        }

        if ($options['trace']) {
            $logger->warning((string) new ErrorException($message, 0, $level, $file, $line));

            return;
        }

        $logger->warning(sprintf('%s in %s on line %s', $message, $file, $line));
    }

    /**
     * Determine if deprecation errors should be ignored.
     *
     * @return bool
     */
    protected function shouldIgnoreDeprecationErrors()
    {
        return ! class_exists(LogManager::class)
            || ! $this->app->hasBeenBootstrapped()
            || $this->app->runningUnitTests();
    }

    /**
     * Get the deprecations logging options from the "logging.deprecations" configuration.
     *
     * Accepts a channel name or a ['channel' => ..., 'trace' => ...] array (Laravel format).
     * A "null" or false channel discards deprecations. Without a channel, deprecations are
     * logged to the "deprecations" channel when WP_DEBUG is enabled and discarded otherwise.
     *
     * @return array{channel: string, trace: bool}
     */
    protected function deprecationLoggingOptions()
    {
        $options = $this->app['config']->get('logging.deprecations');

        if (! is_array($options)) {
            $options = ['channel' => $options];
        }

        $channel = $options['channel'] ?? null;

        if ($channel === false) {
            $channel = 'null';
        } elseif (! is_string($channel) || $channel === '') {
            $channel = (defined('WP_DEBUG') && WP_DEBUG) ? 'deprecations' : 'null';
        }

        return [
            'channel' => $channel,
            'trace' => (bool) ($options['trace'] ?? false),
        ];
    }

    /**
     * Resolve the log channel to use for deprecations, or null to discard them.
     *
     * @param string $channel
     *
     * @return string|null
     */
    protected function resolveDeprecationChannel($channel)
    {
        if ($channel === 'null' || ! $this->app['config']->get("logging.channels.{$channel}")) {
            return null;
        }

        return $channel;
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * @param \Throwable $e
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
     * @param int $level
     *
     * @return bool
     */
    protected function isDeprecation($level)
    {
        return in_array($level, [E_DEPRECATED, E_USER_DEPRECATED]);
    }

    /**
     * Determine if the error level is a warning or notice.
     *
     * @param int $level
     *
     * @return bool
     */
    protected function isWarning($level)
    {
        return in_array($level, [
            E_WARNING,
            E_USER_WARNING,
            E_NOTICE,
            E_USER_NOTICE
        ]);
    }

    /**
     * Determine if the error level should throw an exception.
     *
     * @param int $level
     *
     * @return bool
     */
    protected function shouldThrowException($level)
    {
        return in_array($level, [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
            E_RECOVERABLE_ERROR,
            E_PARSE
        ]);
    }

    /**
     * Get a string representation of the error level.
     *
     * @param int $level
     *
     * @return string
     */
    protected function getErrorLevelString($level)
    {
        $levels = [
            E_WARNING => 'Warning',
            E_USER_WARNING => 'User Warning',
            E_NOTICE => 'Notice',
            E_USER_NOTICE => 'User Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
        ];

        return $levels[$level] ?? 'Unknown Error';
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param int $type
     *
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

    /**
     * Create a new fatal exception instance from an error array.
     *
     * @param array    $error
     * @param int|null $traceOffset
     *
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
     *
     * @param \Throwable $e
     */
    protected function renderForConsole(\Throwable $e)
    {
        $this->getExceptionHandler()->renderForConsole(new ConsoleOutput(), $e);
    }

    /**
     * Render an exception as an HTTP Response and send it.
     *
     * @param \Throwable $e
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
