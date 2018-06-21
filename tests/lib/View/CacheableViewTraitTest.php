<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\CacheableView;
use PHPUnit\Framework\TestCase;

final class CacheableViewTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\CacheableViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->view = new CacheableView(new Value());
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::getSharedMaxAge
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::isCacheable
     */
    public function testDefaultValues(): void
    {
        $this->assertFalse($this->view->isCacheable());
        $this->assertSame(0, $this->view->getSharedMaxAge());
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::isCacheable
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::setIsCacheable
     */
    public function testGetSetIsCacheable(): void
    {
        $this->view->setIsCacheable(true);
        $this->assertTrue($this->view->isCacheable());
    }

    /**
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::getSharedMaxAge
     * @covers \Netgen\BlockManager\View\CacheableViewTrait::setSharedMaxAge
     */
    public function testGetSetSharedMaxAge(): void
    {
        $this->view->setSharedMaxAge(42);
        $this->assertSame(42, $this->view->getSharedMaxAge());
    }
}
