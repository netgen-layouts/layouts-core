<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\View\Stubs\CacheableView;
use PHPUnit\Framework\TestCase;

final class CacheableViewTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\CacheableViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->view = new CacheableView();
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::getSharedMaxAge
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::isCacheable
     */
    public function testDefaultValues()
    {
        $this->assertTrue($this->view->isCacheable());
        $this->assertEquals(0, $this->view->getSharedMaxAge());
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::isCacheable
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::setIsCacheable
     */
    public function testGetSetIsCacheable()
    {
        $this->view->setIsCacheable(false);
        $this->assertFalse($this->view->isCacheable());
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::getSharedMaxAge
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::setSharedMaxAge
     */
    public function testGetSetSharedMaxAge()
    {
        $this->view->setSharedMaxAge(42);
        $this->assertEquals(42, $this->view->getSharedMaxAge());
    }
}
