<?php

use Themosis\Html\HtmlBuilder as Html;

class HtmlTest extends PHPUnit_Framework_TestCase
{
    public function testEntities()
    {
        $html = new Html();

        $s = $html->entities('<strong>Test</strong>');
        $this->assertEquals('&lt;strong&gt;Test&lt;/strong&gt;', $s);

        $s = $html->entities('"#000\' onload=\'alert(document.cookie)"');
        $this->assertEquals('&quot;#000&#039; onload=&#039;alert(document.cookie)&quot;', $s);
    }

    public function testAttributes()
    {
        $html = new Html();

        // One attribute.
        $atts = $html->attributes(['id' => 'test']);
        $this->assertEquals(' id="test"', $atts);

        // Multiple attributes.
        $atts = $html->attributes(['id' => 'awesome', 'class' => 'highlight', 'title' => 'SomeTitleValue']);
        $this->assertEquals(' id="awesome" class="highlight" title="SomeTitleValue"', $atts);

        // One attribute without key.
        $atts = $html->attributes(['multiple']);
        $this->assertEquals(' multiple', $atts);

        // Multiple attributes without key.
        $atts = $html->attributes(['multiple', 'required', 'checked']);
        $this->assertEquals(' multiple required checked', $atts);

        // No attributes.
        $atts = $html->attributes([]);
        $this->assertEquals('', $atts);

        // Key with empty value.
        $atts = $html->attributes(['id' => null, 'class' => '']);
        $this->assertEquals('', $atts);

        // Mix with and without keys, empty values.
        $atts = $html->attributes(['id' => 'awesome', 'class' => '', 'required', 'href' => 'some/uri/', 'checked']);
        $this->assertEquals(' id="awesome" required href="some/uri/" checked', $atts);
    }
}