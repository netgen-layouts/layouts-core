<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use Netgen\Layouts\HttpCache\Layout\IdProviderInterface;
use Netgen\Layouts\HttpCache\VarnishClient;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
     * @var \Netgen\Layouts\HttpCache\VarnishClient
     */
    private $client;

    protected function setUp(): void
    {
        $this->fosInvalidatorMock = $this->createMock(CacheInvalidator::class);
        $this->idProviderMock = $this->createMock(IdProviderInterface::class);

        $this->client = new VarnishClient(
            $this->fosInvalidatorMock,
            $this->idProviderMock
        );
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::__construct
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateLayouts
     */
    public function testInvalidateLayouts(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();
        $uuid3 = Uuid::uuid4();
        $uuid4 = Uuid::uuid4();

        $this->idProviderMock
            ->expects(self::at(0))
            ->method('provideIds')
            ->with(self::identicalTo($uuid1->toString()))
            ->willReturn([$uuid1->toString(), $uuid3->toString(), $uuid4->toString()]);

        $this->idProviderMock
            ->expects(self::at(1))
            ->method('provideIds')
            ->with(self::identicalTo($uuid2->toString()))
            ->willReturn([$uuid2->toString()]);

        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('invalidate')
            ->with(
                self::identicalTo(
                    [
                        'X-Layout-Id' => sprintf(
                            '^(%s|%s|%s|%s)$',
                            $uuid1->toString(),
                            $uuid3->toString(),
                            $uuid4->toString(),
                            $uuid2->toString()
                        ),
                    ]
                )
            );

        $this->client->invalidateLayouts([$uuid1->toString(), $uuid2->toString()]);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateLayouts
     */
    public function testInvalidateLayoutsWithEmptyLayoutIds(): void
    {
        $this->idProviderMock
            ->expects(self::never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects(self::never())
            ->method('invalidate');

        $this->client->invalidateLayouts([]);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts(): void
    {
        $this->idProviderMock
            ->expects(self::never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('invalidate')
            ->with(
                self::identicalTo(
                    [
                        'X-Layout-Id' => '.*',
                    ]
                )
            );

        $this->client->invalidateAllLayouts();
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateBlocks
     */
    public function testInvalidateBlocks(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('invalidate')
            ->with(
                self::identicalTo(
                    [
                        'X-Block-Id' => sprintf('^(%s|%s)$', $uuid1->toString(), $uuid2->toString()),
                    ]
                )
            );

        $this->client->invalidateBlocks([$uuid1->toString(), $uuid2->toString()]);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateBlocks
     */
    public function testInvalidateBlocksWithEmptyBlockIds(): void
    {
        $this->fosInvalidatorMock
            ->expects(self::never())
            ->method('invalidate');

        $this->client->invalidateBlocks([]);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocks(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('invalidate')
            ->with(
                self::identicalTo(
                    [
                        'X-Origin-Layout-Id' => sprintf('^(%s|%s)$', $uuid1->toString(), $uuid2->toString()),
                    ]
                )
            );

        $this->client->invalidateLayoutBlocks([$uuid1->toString(), $uuid2->toString()]);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocksWithEmptyLayoutIds(): void
    {
        $this->fosInvalidatorMock
            ->expects(self::never())
            ->method('invalidate');

        $this->client->invalidateLayoutBlocks([]);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks(): void
    {
        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('invalidate')
            ->with(
                self::identicalTo(
                    [
                        'X-Block-Id' => '.*',
                    ]
                )
            );

        $this->client->invalidateAllBlocks();
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::commit
     */
    public function testCommit(): void
    {
        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('flush');

        self::assertTrue($this->client->commit());
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::commit
     */
    public function testCommitReturnsFalse(): void
    {
        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('flush')
            ->willThrowException(new ExceptionCollection());

        self::assertFalse($this->client->commit());
    }
}
