<?php

namespace Netgen\BlockManager\Tests\HttpCache;

use Netgen\BlockManager\HttpCache\Block\InvalidatorInterface as BlockInvalidatorInterface;
use Netgen\BlockManager\HttpCache\Client;
use Netgen\BlockManager\HttpCache\Layout\InvalidatorInterface as LayoutInvalidatorInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutInvalidatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockInvalidatorMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\Client
     */
    protected $client;

    public function setUp()
    {
        $this->layoutInvalidatorMock = $this->createMock(LayoutInvalidatorInterface::class);
        $this->blockInvalidatorMock = $this->createMock(BlockInvalidatorInterface::class);

        $this->client = new Client($this->layoutInvalidatorMock, $this->blockInvalidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Client::__construct
     * @covers \Netgen\BlockManager\HttpCache\Client::invalidateLayouts
     */
    public function testInvalidateLayouts()
    {
        $this->layoutInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with($this->equalTo(array(24, 42)));

        $this->client->invalidateLayouts(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Client::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts()
    {
        $this->layoutInvalidatorMock
            ->expects($this->once())
            ->method('invalidateAll');

        $this->client->invalidateAllLayouts();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Client::invalidateBlocks
     */
    public function testInvalidateBlocks()
    {
        $this->blockInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with($this->equalTo(array(24, 42)));

        $this->client->invalidateBlocks(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Client::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks()
    {
        $this->blockInvalidatorMock
            ->expects($this->once())
            ->method('invalidateAll');

        $this->client->invalidateAllBlocks();
    }
}
