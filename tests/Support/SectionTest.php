<?php

namespace Themosis\Tests\Support;

use PHPUnit\Framework\TestCase;
use Themosis\Support\Section;

class SectionTest extends TestCase
{
    public function testBasicSectionCreation()
    {
        $section = new Section('default');

        $this->assertEquals('default', $section->getId());
    }
}
