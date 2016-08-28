<?php

class NonExistingRouteConditionException extends Exception
{
	/**
	 * NonExistingRouteCondition constructor.
	 */
	public function __construct()
	{
		parent::__construct('Non-existing route condition has been given');
	}
}