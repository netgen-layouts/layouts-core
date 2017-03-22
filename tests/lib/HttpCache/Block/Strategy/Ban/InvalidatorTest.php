<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\Strategy\Ban;

use FOS\HttpCache\CacheInvalidator;
use Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator;
use PHPUnit\Framework\TestCase;

class InvalidatorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fosInvalidatorMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator
     */
    protected $invalidator;

    public function setUp()
    {
        $this->fosInvalidatorMock = $this->createMock(CacheInvalidator::class);

        $this->invalidator = new Invalidator($this->fosInvalidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator::__construct
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator::invalidate
     */
    public function testInvalidate()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Block-Id' => '^(24|42)$',
                    )
                )
            );

        $this->invalidator->invalidate(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator::invalidate
     */
    public function testInvalidateWithEmptyBlockIds()
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->invalidator->invalidate(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocks()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Origin-Layout-Id' => '^(24|42)$',
                    )
                )
            );

        $this->invalidator->invalidateLayoutBlocks(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocksWithEmptyLayoutIds()
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->invalidator->invalidateLayoutBlocks(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Invalidator::invalidateAll
     */
    public function testInvalidateAll()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Block-Id' => '.*',
                    )
                )
            );

        $this->invalidator->invalidateAll();
    }
}
