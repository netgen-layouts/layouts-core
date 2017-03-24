<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\View\Stubs\CacheableView;
use PHPUnit\Framework\TestCase;

class CacheableViewTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\CacheableViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new CacheableView();
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::isCacheable
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::getSharedMaxAge
     */
    public function testDefaultValues()
    {
        $this->assertTrue($this->view->isCacheable());
        $this->assertEquals(0, $this->view->getSharedMaxAge());
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::setIsCacheable
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::isCacheable
     */
    public function testGetSetIsCacheable()
    {
        $this->view->setIsCacheable(false);
        $this->assertFalse($this->view->isCacheable());
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::setSharedMaxAge
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::getSharedMaxAge
     */
    public function testGetSetSharedMaxAge()
    {
        $this->view->setSharedMaxAge(42);
        $this->assertEquals(42, $this->view->getSharedMaxAge());
    }
}
