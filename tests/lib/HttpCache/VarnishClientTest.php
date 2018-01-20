<?php

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

    public function setUp()
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
    public function testInvalidateLayouts()
    {
        $this->idProviderMock
            ->expects($this->at(0))
            ->method('provideIds')
            ->with($this->equalTo(24))
            ->will($this->returnValue(array(24, 25, 26)));

        $this->idProviderMock
            ->expects($this->at(1))
            ->method('provideIds')
            ->with($this->equalTo(42))
            ->will($this->returnValue(array(42)));

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Layout-Id' => '^(24|25|26|42)$',
                    )
                )
            );

        $this->client->invalidateLayouts(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateLayouts
     */
    public function testInvalidateLayoutsWithEmptyLayoutIds()
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateLayouts(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts()
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Layout-Id' => '.*',
                    )
                )
            );

        $this->client->invalidateAllLayouts();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateBlocks
     */
    public function testInvalidateBlocks()
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

        $this->client->invalidateBlocks(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateBlocks
     */
    public function testInvalidateBlocksWithEmptyBlockIds()
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateBlocks(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateLayoutBlocks
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

        $this->client->invalidateLayoutBlocks(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocksWithEmptyLayoutIds()
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateLayoutBlocks(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks()
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

        $this->client->invalidateAllBlocks();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::commit
     */
    public function testCommit()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->client->commit());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\VarnishClient::commit
     */
    public function testCommitReturnsFalse()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new ExceptionCollection()));

        $this->assertFalse($this->client->commit());
    }
}
