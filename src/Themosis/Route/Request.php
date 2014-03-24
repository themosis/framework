<?php
namespace Themosis\Route;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

defined('DS') or die('No direct script access.');

class Request extends SymfonyRequest
{	
	/**
	 * The global request object
	*/
	public static $foundation;

	/**
	 * Get the Symfony HttpFoundation Request instance.
	 *
	 * @return HttpFoundation\Request
	 */
	public static function foundation()
	{
		return static::$foundation;
	}
}