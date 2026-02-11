<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\Invalidator;
use Netgen\Layouts\HttpCache\Layout\IdProviderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Invalidator::class)]
final class InvalidatorTest extends TestCase
{
    private Stub&IdProviderInterface $idProviderStub;

    protected function setUp(): void
    {
        $this->idProviderStub = self::createStub(IdProviderInterface::class);
    }

    public function testInvalidateLayouts(): void
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();
        $uuid3 = Uuid::v7();
        $uuid4 = Uuid::v7();

        $this->idProviderStub
            ->method('provideIds')
            ->willReturnMap(
                [
                    [$uuid1->toString(), [$uuid1->toString(), $uuid3->toString(), $uuid4->toString()]],
                    [$uuid2->toString(), [$uuid2->toString()]],
                ],
            );

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->once())
            ->method('purge')
            ->with(
                self::identicalTo(
                    [
                        'ngl-layout-' . $uuid1->toString(),
                        'ngl-layout-' . $uuid3->toString(),
                        'ngl-layout-' . $uuid4->toString(),
                        'ngl-layout-' . $uuid2->toString(),
                    ],
                ),
            );

        $invalidator = new Invalidator(
            $clientMock,
            $this->idProviderStub,
        );

        $invalidator->invalidateLayouts([$uuid1->toString(), $uuid2->toString()]);
    }

    public function testInvalidateLayoutsWithEmptyLayoutIds(): void
    {
        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->never())
            ->method('purge');

        $invalidator = new Invalidator(
            $clientMock,
            $this->idProviderStub,
        );

        $invalidator->invalidateLayouts([]);
    }

    public function testInvalidateBlocks(): void
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->once())
            ->method('purge')
            ->with(
                self::identicalTo(
                    [
                        'ngl-block-' . $uuid1->toString(),
                        'ngl-block-' . $uuid2->toString(),
                    ],
                ),
            );

        $invalidator = new Invalidator(
            $clientMock,
            $this->idProviderStub,
        );

        $invalidator->invalidateBlocks([$uuid1->toString(), $uuid2->toString()]);
    }

    public function testInvalidateBlocksWithEmptyBlockIds(): void
    {
        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->never())
            ->method('purge');

        $invalidator = new Invalidator(
            $clientMock,
            $this->idProviderStub,
        );

        $invalidator->invalidateBlocks([]);
    }

    public function testInvalidateLayoutBlocks(): void
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->once())
            ->method('purge')
            ->with(
                self::identicalTo(
                    [
                        'ngl-origin-layout-' . $uuid1->toString(),
                        'ngl-origin-layout-' . $uuid2->toString(),
                    ],
                ),
            );

        $invalidator = new Invalidator(
            $clientMock,
            $this->idProviderStub,
        );

        $invalidator->invalidateLayoutBlocks([$uuid1->toString(), $uuid2->toString()]);
    }

    public function testInvalidateLayoutBlocksWithEmptyLayoutIds(): void
    {
        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->never())
            ->method('purge');

        $invalidator = new Invalidator(
            $clientMock,
            $this->idProviderStub,
        );

        $invalidator->invalidateLayoutBlocks([]);
    }

    public function testCommit(): void
    {
        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->once())
            ->method('commit')
            ->willReturn(true);

        $invalidator = new Invalidator(
            $clientMock,
            $this->idProviderStub,
        );

        self::assertTrue($invalidator->commit());
    }
}
