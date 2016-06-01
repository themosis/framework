<?php

use Themosis\Html\FormBuilder as Form;
use Themosis\Html\HtmlBuilder as Html;
use Themosis\Foundation\Request;

class FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Form instance in order to test
     * input methods.
     *
     * @var \Themosis\Html\FormBuilder
     */
    protected $form;

    public function setUp()
    {
        $request = Request::createFromBase(Request::create('http://www.themosis.com'));
        $html = new Html();
        $this->form = new Form($html, $request);
    }

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

    protected function getFormSSLDomain()
    {
        $request = Request::createFromBase(Request::create('https://www.themosis.test'));
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
        $nonce = wp_create_nonce('form');

        $this->assertEquals('<form action="http://somedomain.com/" method="POST" accept-charset="UTF-8"><input type="hidden" id="_themosisnonce" name="_themosisnonce" value="'.$nonce.'" /><input type="hidden" name="_wp_http_referer" value="" />', $open);
    }

    /**
     * Test default open method on SSL request.
     */
    public function testOpenTagWithSSL()
    {
        // Default, no arguments.
        $form = $this->getFormSSLDomain();
        $open = $form->open('contact', 'post', true);
        $nonce = wp_create_nonce('form');

        $this->assertEquals('<form action="https://www.themosis.test/contact" method="POST" accept-charset="UTF-8"><input type="hidden" id="_themosisnonce" name="_themosisnonce" value="'.$nonce.'" /><input type="hidden" name="_wp_http_referer" value="" />', $open);
    }

    public function testCloseFormTag()
    {
        $this->assertEquals('</form>', $this->form->close());
    }

    public function testLabelTag()
    {
        $this->assertEquals('<label for="input-id">Message</label>', $this->form->label('Message', ['for' => 'input-id']));
    }

    public function testInputTag()
    {
        $this->assertEquals('<input type="text" name="email" value="somevalue">', $this->form->input('text', 'email', 'somevalue'));
        $this->assertEquals('<input type="number" name="age" value="30" min="5" max="40">', $this->form->input('number', 'age', 30, ['min' => 5, 'max' => 40]));
    }

    public function testTextInput()
    {
        $this->assertEquals('<input type="text" name="network" value="facebook">', $this->form->text('network', 'facebook'));
        $this->assertEquals('<input type="text" name="network" value="twitter" data-open="runIt()">', $this->form->text('network', 'twitter', ['data-open' => 'runIt()']));
    }

    public function testPassword()
    {
        $this->assertEquals('<input type="password" name="hashme" value="secret">', $this->form->password('hashme', 'secret'));
        $this->assertEquals('<input type="password" name="passwd" value="treasure" id="passField">', $this->form->password('passwd', 'treasure', ['id' => 'passField']));
    }

    public function testEmail()
    {
        $this->assertEquals('<input type="email" name="mailme" value="john@doe.me" placeholder="Please enter your email...">', $this->form->email('mailme', 'john@doe.me'));
        $this->assertEquals('<input type="email" name="support-email" value="support@help.com" placeholder="Support email..." required>', $this->form->email('support-email', 'support@help.com', ['placeholder' => 'Support email...', 'required']));
    }

    public function testNumber()
    {
        $this->assertEquals('<input type="number" name="sku" value="123">', $this->form->number('sku', 123));
        $this->assertEquals('<input type="number" name="price" data-onComplete="sum()">', $this->form->number('price', '', ['data-onComplete' => 'sum()']));
    }
    
    public function testDate()
    {
        $this->assertEquals('<input type="date" name="calendar" value="10/07/1986">', $this->form->date('calendar', '10/07/1986'));
    }

    public function testHidden()
    {
        $this->assertEquals('<input type="hidden" name="catchme" value="ifyoucan">', $this->form->hidden('catchme', 'ifyoucan'));
        $this->assertEquals('<input type="hidden" name="hidden" value="treasure" class="hidden">', $this->form->hidden('hidden', 'treasure', ['class' => 'hidden']));
    }

    public function testCheckboxInput()
    {
        // Single checkbox not checked
        $this->assertEquals('<label><input type="checkbox" name="activate" value="toggle">Toggle</label>', $this->form->checkbox('activate', 'toggle', 'some'));

        // Single checkbox not checked with empty value.
        $this->assertEquals('<label><input type="checkbox" name="activate" value="toggle">Toggle</label>', $this->form->checkbox('activate', 'toggle'));

        // Single checkbox checked
        $this->assertEquals('<label><input type="checkbox" name="feature" value="enable" checked>Enable</label>', $this->form->checkbox('feature', 'enable', 'enable'));

        // Single checkbox not checked with custom label.
        $this->assertEquals('<label><input type="checkbox" name="feature" value="enable">Enable or not the feature.</label>', $this->form->checkbox('feature', ['enable' => 'Enable or not the feature.']));

        // Single checkbox checked with custom label.
        $this->assertEquals('<label><input type="checkbox" name="feature" value="enable" class="custom-field" checked>Activate this feature?</label>', $this->form->checkbox('feature', ['enable' => 'Activate this feature?'], 'enable', ['class' => 'custom-field']));

        // Multiple checkbox, none is checked.
        $this->assertEquals('<label><input type="checkbox" name="colors[]" value="red">Red</label><label><input type="checkbox" name="colors[]" value="green">Green</label><label><input type="checkbox" name="colors[]" value="blue">Blue</label>', $this->form->checkbox('colors', ['red', 'green', 'blue']));

        // Multiple checkbox, one value.
        $this->assertEquals('<label><input type="checkbox" name="colors[]" value="red">Red</label><label><input type="checkbox" name="colors[]" value="green" checked>Green</label><label><input type="checkbox" name="colors[]" value="blue">Blue</label>', $this->form->checkbox('colors', ['red', 'green', 'blue'], 'green'));

        // Multiple checkbox, multiple values.
        $this->assertEquals('<label><input type="checkbox" name="colors[]" value="red" class="color-cb" checked>Red</label><label><input type="checkbox" name="colors[]" value="green" class="color-cb">Green</label><label><input type="checkbox" name="colors[]" value="blue" class="color-cb" checked>Blue</label>', $this->form->checkbox('colors', ['red', 'green', 'blue'], ['red', 'blue'], ['class' => 'color-cb']));

        // Multiple checkbox, custom label and multiple values.
        $this->assertEquals('<label><input type="checkbox" name="colors[]" value="red" checked>Rouge</label><label><input type="checkbox" name="colors[]" value="green" checked>Vert</label><label><input type="checkbox" name="colors[]" value="blue">Bleu</label>', $this->form->checkbox('colors', ['red' => 'Rouge', 'green' => 'Vert', 'blue' => 'Bleu'], ['red', 'green']));
    }
}
