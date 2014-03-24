<?php
namespace Themosis\Session;

class Session
{
	/**
	 * Action identitfier for a nonce field
	*/
	const nonceAction = 'themosis-nonce-action';

	/**
	 * Name attribute for a nonce field
	*/
	const nonceName = '_themosisnonce';

	/**
	 * Keep references of all Session instances
	*/
	private static $instances = array();
	
	/**
	 * Private constructor. Avoid building instances using the
	 * 'new' keyword.
	*/
	private function __construct()
	{

	}



}