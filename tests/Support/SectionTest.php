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
            new \stdClass(),
        ]);
        $section->setView('com.site.component.default');
        $section->setViewData([
            'title' => 'A section name',
            'desc' => 'Something useful to say',
        ]);

        $this->assertEquals('default', $section->getId());
        $this->assertEmpty($section->getTitle());
        $this->assertEquals($items, $section->getItems());
        $this->assertEquals('com.site.component.default', $section->getView());
        $this->assertEquals([
            'title' => 'A section name',
            'desc' => 'Something useful to say',
            '__section' => $section,
        ], $section->getViewData());

        $this->assertEquals(2, count($section));

        foreach ($section as $item) {
            $this->assertInstanceOf('stdClass', $item);
        }
    }

    public function testBasicSectionWithTitle()
    {
        $section = new Section('custom', 'Custom Title');

        $this->assertEquals('Custom Title', $section->getTitle());

        $section->setTitle('Another Title');

        $this->assertEquals('Another Title', $section->getTitle());
    }

    public function testSectionCanHaveItemAtConstruction()
    {
        $section = new Section('special', 'Special', [
            'item 1',
            'item 2',
        ]);

        $this->assertEquals([
            'item 1',
            'item 2',
        ], $section->getItems());
        $this->assertEquals('special', $section->getId());
        $this->assertEquals('Special', $section->getTitle());
    }

    public function testSectionHaveItems()
    {
        $section = new Section('empty');
        $this->assertFalse($section->hasItems());

        $section = new Section('custom', 'Custom', [1, 2]);
        $this->assertTrue($section->hasItems());

        $section = new Section('panel');
        $section->setItems([3, 4]);
        $this->assertTrue($section->hasItems());
    }
}
