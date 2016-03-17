<?php

use Themosis\Html\FormBuilder as Form;
use Themosis\Html\HtmlBuilder as Html;
use Themosis\Session\Session;
use Themosis\Core\Request;

class FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Initialize a FormBuilder instance.
     */
    public function setUp()
    {
        WP_Mock::setUp();
    }

    public function tearDown()
    {
        WP_Mock::tearDown();
    }

    /**
     * Return a form instance working on a domain without SSL
     * and no query arguments and sub-uri. (Like a home page)
     */
    protected function getFormNonSSLDomain()
    {
        $request = Request::createFromBase(Request::create('http://somedomain.com'));
        $html = new Html();
        return new Form($html, $request);
    }

    /**
     * Test default open method without arguments on non SSL request.
     */
    public function testOpenTagDefaultArgsNonSSL()
    {
        WP_Mock::wpPassthruFunction('esc_url');

        WP_Mock::wpFunction('is_ssl', [
            'return'    => false
        ]);

        WP_Mock::wpFunction('wp_nonce_field', [
            'args'      => [Session::nonceAction, Session::nonceName, true, false],
            'return'    => '<input type="hidden" id="_themosisnonce" name="_themosisnonce" value="123abc789d"><input type="hidden" name="_wp_http_referer" value="/">'
        ]);

        // Default, no arguments.
        $form = $this->getFormNonSSLDomain();
        $open = $form->open();
        $this->assertEquals('<form action="http://somedomain.com/" method="POST" accept-charset="UTF-8"><input type="hidden" id="_themosisnonce" name="_themosisnonce" value="123abc789d"><input type="hidden" name="_wp_http_referer" value="/">', $open);
    }
}