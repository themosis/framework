<?php

namespace Themosis\Tests\Page;

use PHPUnit\Framework\TestCase;
use Themosis\Page\PageFactory;

class PageTest extends TestCase
{
    public function getActionMock()
    {
        return $this->getMockBuilder(\Themosis\Hook\ActionBuilder::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getFactory($action)
    {
        return new PageFactory($action);
    }

    public function testCreateACustomPage()
    {
        $action = $this->getActionMock();
        $factory = $this->getFactory($action);

        $page = $factory->make('a-page', 'Custom Page');

        $this->assertInstanceOf(\Themosis\Page\Page::class, $page);
        $this->assertEquals('a-page', $page->getSlug());
        $this->assertEquals('Custom Page', $page->getTitle());
        $this->assertEquals('Custom Page', $page->getMenu());
        $this->assertEquals('manage_options', $page->getCapability());
        $this->assertEquals('dashicons-admin-generic', $page->getIcon());
        $this->assertEquals(21, $page->getPosition());
        $this->assertNull($page->getParent());
        $this->assertFalse($page->isNetwork());

        $action->expects($this->once())->method('add');

        $page->set();
    }

    public function testCreateANetworkPage()
    {
        $action = $this->getActionMock();
        $factory = $this->getFactory($action);

        $page = $factory->make('settings', 'Network Options')
            ->network();

        $this->assertTrue($page->isNetwork());

        $action->expects($this->once())->method('add');

        $page->set();
    }
}
