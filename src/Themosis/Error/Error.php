<?php
namespace Themosis\Error;

use Exception;
	
class Error extends Exception
{
	/**
	 * Handle PHP exceptions
	 * 
	 * @param \Exception $exception The thrown exception.
	 * @param bool $trace Display the stack trace or not.
	 * @return void
	 */
    public static function exception($exception, $trace = true)
    {
		ob_get_level() and ob_end_clean();

		// If detailed errors are enabled, we'll just format the exception into
		// a simple error message and display it on the screen. We don't use a
		// View in case the problem is in the View class.
        $display = defined('THEMOSIS_ERROR_DISPLAY') ? THEMOSIS_ERROR_DISPLAY : false;
		if ($display)
        {
			echo "<html><h2>Unhandled Exception</h2>
				  <h3>Message:</h3>
				  <pre>".$exception->getMessage()."</pre>
				  <h3>Location:</h3>
				  <pre>".$exception->getFile()." on line ".$exception->getLine()."</pre>";

			if ($trace)
			{
				echo "
				  <h3>Stack Trace:</h3>
				  <pre>".$exception->getTraceAsString()."</pre></html>";
			}
		}
        else
        {
			// RETURN 500 Error
		}

		exit(1);
    }

    /**
	 * Handle a native PHP error as an ErrorException.
	 *
	 * @param int $code The error code.
	 * @param string $error The error message.
	 * @param string $file The error file.
	 * @param int $line The error line.
	 * @return void
	 */
	public static function native($code, $error, $file, $line)
	{
		if (error_reporting() === 0) return;

		// For a PHP error, we'll create an ErrorException and then feed that
		// exception to the exception method, which will create a simple view
		// of the exception details for the developer.
		$exception = new \ErrorException($error, $code, 0, $file, $line);

		static::exception($exception);
	}

	/**
	 * Handle the PHP shutdown event.
	 *
	 * @return void
	 */
	public static function shutdown()
	{
		// If a fatal error occurred that we have not handled yet, we will
		// create an ErrorException and feed it to the exception handler,
		// as it will not yet have been handled.
		$error = error_get_last();

		if (!is_null($error))
        {
			extract($error, EXTR_SKIP);

			static::exception(new \ErrorException($message, $type, 0, $file, $line), false);
		}
	}
}