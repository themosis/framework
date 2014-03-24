<?php
namespace Themosis\Html;

use Themosis\Configuration\Application;
use Themosis\Route\Request;
use Themosis\Session\Session;

defined('DS') or die('No direct script access.');

class Form
{
	/**
	 * Build opening tags for a form
	 * 
	 * @param string
	 * @param string
	 * @param boolean Default value is false
	 * @param array
	 * @return string
	*/
	public static function open($action = null, $method = 'POST', $ssl = false, $attributes = array())
	{
		$attributes['action'] = static::action($action, $ssl);
		$attributes['method'] = static::method($method);

		// If a character encoding has not been specified in the attributes, we will
		// use the default encoding as specified in the application configuration
		// file for the "accept-charset" attribute.
		if (!array_key_exists('accept-charset', $attributes)) {
			$attributes['accept-charset'] = Application::get('encoding');
		}

		// ADD NONCE FIELDS
		// IF 'POST' METHOD
		// HELP TO AVOID CSRF
		$append = '';

		if ($attributes['method'] === 'POST') {
			$append = wp_nonce_field(Session::nonceAction, Session::nonceName, true, false);
		}

		return '<form'.Html::attributes($attributes).'>'.$append;
	}

	/**
	 * Build the closing tag
	 * 
	 * @return string
	*/
	public static function close()
	{
		return '</form>';
	}

	/**
	 * Define the action attribute
	 * 
	 * @param string
	 * @param boolean $ssl Tell to set the URL ot https or not.
	 * @return string
	*/
	public static function action($action, $ssl)
	{	
		$action = trim($action);
		$ssl = (bool) $ssl;

		// Check the given path
		// If none given, set to the current page url
		$uri = ($action === null || empty($action)) ? Request::foundation()->getPathInfo() : '/'.trim($action, '/').'/';

		// Build the action url
		// Check if we'are using ssl or not and build the url.
		$action = (is_ssl() || $ssl) ? 'https://'.Request::foundation()->getHttpHost().$uri : 'http://'.Request::foundation()->getHttpHost().$uri;

		return $action;
	}

	/**
	 * Define the form method attribute
	 * 
	 * @param string
	 * @return string
	*/
	public static function method($method)
	{
		$method = strtoupper($method);

		return ($method === 'POST') ? $method : 'GET';
	}

}