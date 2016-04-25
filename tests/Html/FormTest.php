<?php

use Themosis\Html\FormBuilder as Form;
use Themosis\Html\HtmlBuilder as Html;
use Themosis\Session\Session;
use Themosis\Foundation\Request;

class FormTest extends WP_UnitTestCase
{
    /**
     * Return a form instance working on a domain without SSL
     * and no query arguments and sub-uri. (Like a home page).
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
        // Default, no arguments.
        $form = $this->getFormNonSSLDomain();
        $open = $form->open();
        $nonce = wp_create_nonce(Session::nonceAction);
        $this->assertEquals('<form action="http://somedomain.com/" method="POST" accept-charset="UTF-8"><input type="hidden" id="_themosisnonce" name="_themosisnonce" value="'.$nonce.'" /><input type="hidden" name="_wp_http_referer" value="" />', $open);
    }
}
