<?php

namespace Themosis\Tests\Support;

use PHPUnit\Framework\TestCase;
use Themosis\Support\Section;

class SectionTest extends TestCase
{
    public function testBasicSectionCreation()
    {
        $section = new Section('default');
        $section->setItems($items = [
            new \stdClass(),
            new \stdClass()
        ]);
        $section->setView('com.site.component.default');
        $section->setViewData([
            'title' => 'A section name',
            'desc' => 'Something useful to say'
        ]);

        $this->assertEquals('default', $section->getId());
        $this->assertEquals($items, $section->getItems());
        $this->assertEquals('com.site.component.default', $section->getView());
        $this->assertEquals([
            'title' => 'A section name',
            'desc' => 'Something useful to say',
            '__section' => $section
        ], $section->getViewData());

        $this->assertEquals(2, count($section));

        foreach ($section as $item) {
            $this->assertInstanceOf('stdClass', $item);
        }
    }
}
