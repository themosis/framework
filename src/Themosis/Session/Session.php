<?php
namespace Themosis\Session;

class Session
{
	/**
	 * Action identifier for a nonce field
	*/
	const nonceAction = 'themosis-nonce-action';

	/**
	 * Name attribute for a nonce field
	*/
	const nonceName = '_themosisnonce';
	
	/**
	 * Private constructor. Avoid building instances using the
	 * 'new' keyword.
	 */
	private function __construct()
	{

	}

}