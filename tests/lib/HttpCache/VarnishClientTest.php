<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use Netgen\BlockManager\HttpCache\Layout\IdProviderInterface;
use Netgen\BlockManager\HttpCache\VarnishClient;
use PHPUnit\Framework\TestCase;

final class VarnishClientTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $fosInvalidatorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $idProviderMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\VarnishClient
     */
    private $client;

    public function setUp(): void
    {
        $this->fosInvalidatorMock = $this->createMock(CacheInvalidator::class);
        $this->idProviderMock = $this->createMock(IdProviderInterface::class);

        $this->client = new VarnishClient(
            $this->fosInvalidatorMock,
            $this->idProviderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::__construct
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateLayouts
     */
    public function testInvalidateLayouts(): void
    {
        $this->idProviderMock
            ->expects($this->at(0))
            ->method('provideIds')
            ->with($this->identicalTo(24))
            ->will($this->returnValue([24, 25, 26]));

        $this->idProviderMock
            ->expects($this->at(1))
            ->method('provideIds')
            ->with($this->identicalTo(42))
            ->will($this->returnValue([42]));

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->identicalTo(
                    [
                        'X-Layout-Id' => '^(24|25|26|42)$',
                    ]
                )
            );

        $this->client->invalidateLayouts([24, 42]);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateLayouts
     */
    public function testInvalidateLayoutsWithEmptyLayoutIds(): void
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateLayouts([]);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts(): void
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->identicalTo(
                    [
                        'X-Layout-Id' => '.*',
                    ]
                )
            );

        $this->client->invalidateAllLayouts();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateBlocks
     */
    public function testInvalidateBlocks(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->identicalTo(
                    [
                        'X-Block-Id' => '^(24|42)$',
                    ]
                )
            );

        $this->client->invalidateBlocks([24, 42]);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateBlocks
     */
    public function testInvalidateBlocksWithEmptyBlockIds(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateBlocks([]);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocks(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->identicalTo(
                    [
                        'X-Origin-Layout-Id' => '^(24|42)$',
                    ]
                )
            );

        $this->client->invalidateLayoutBlocks([24, 42]);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocksWithEmptyLayoutIds(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateLayoutBlocks([]);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->identicalTo(
                    [
                        'X-Block-Id' => '.*',
                    ]
                )
            );

        $this->client->invalidateAllBlocks();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::commit
     */
    public function testCommit(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->client->commit());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::commit
     */
    public function testCommitReturnsFalse(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new ExceptionCollection()));

        $this->assertFalse($this->client->commit());
    }
}
