<?php
namespace Themosis\Html;

use Themosis\Configuration\Application;
use Themosis\Route\Request;
use Themosis\Session\Session;

class FormBuilder {

    /**
     * Build opening tags for a form
     *
     * @param string $action The action attribute value.
     * @param string $method The request method. Default to 'POST'.
     * @param bool $ssl Default value is false. True converts to https URL.
     * @param array An array of form attributes.
     * @return string The <form> open tag.
     */
    public function open($action = null, $method = 'POST', $ssl = false, array $attributes = array())
    {
        $attributes['action'] = $this->action($action, $ssl);
        $attributes['method'] = $this->method($method);

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
     * @return string The </form> close tag.
     */
    public function close()
    {
        return '</form>';
    }

    /**
     * Define the action attribute
     *
     * @param string $action The action attribute value.
     * @param bool $ssl Tell to set the URL to https or not.
     * @return string The converted action attribute value.
     */
    private function action($action, $ssl)
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
     * @param string $method The request method.
     * @return string The sanitized request method.
     */
    private function method($method)
    {
        $method = strtoupper($method);

        return ($method === 'POST') ? $method : 'GET';
    }

    /**
     * Build a text input <input type="text">.
     * (not for xhtml)
     *
     * @return string
     */
    public function text()
    {
        return '<input type="text" name="input-name" id="input-id" value="A text value">';
    }

}