<?php

namespace Themosis\Tests\Html;

use PHPUnit\Framework\TestCase;
use Themosis\Html\HtmlBuilder;

class HtmlBuilderTest extends TestCase
{
    public function testHtmlBuilderCanCreateAttributes()
    {
        $html = new HtmlBuilder();

        $this->assertEquals(
            'action="test" method="post"',
            $html->attributes([
                'action' => 'test',
                'method' => 'post'
            ])
        );
    }
}
