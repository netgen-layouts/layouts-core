<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\Invalidator;
use Netgen\Layouts\HttpCache\Layout\IdProviderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Invalidator::class)]
final class InvalidatorTest extends TestCase
{
    private Stub&ClientInterface $clientStub;

    private Stub&IdProviderInterface $idProviderStub;

    private Invalidator $invalidator;

    protected function setUp(): void
    {
        $this->clientStub = self::createStub(ClientInterface::class);
        $this->idProviderStub = self::createStub(IdProviderInterface::class);

        $this->invalidator = new Invalidator(
            $this->clientStub,
            $this->idProviderStub,
        );
    }

    public function testInvalidateLayouts(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();
        $uuid3 = Uuid::uuid4();
        $uuid4 = Uuid::uuid4();

        $this->idProviderStub
            ->method('provideIds')
            ->willReturnMap(
                [
                    [$uuid1->toString(), [$uuid1->toString(), $uuid3->toString(), $uuid4->toString()]],
                    [$uuid2->toString(), [$uuid2->toString()]],
                ],
            );

        $this->clientStub
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

        $this->invalidator->invalidateLayouts([$uuid1->toString(), $uuid2->toString()]);
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
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $this->clientStub
            ->method('purge')
            ->with(
                self::identicalTo(
                    [
                        'ngl-block-' . $uuid1->toString(),
                        'ngl-block-' . $uuid2->toString(),
                    ],
                ),
            );

        $this->invalidator->invalidateBlocks([$uuid1->toString(), $uuid2->toString()]);
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
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $this->clientStub
            ->method('purge')
            ->with(
                self::identicalTo(
                    [
                        'ngl-origin-layout-' . $uuid1->toString(),
                        'ngl-origin-layout-' . $uuid2->toString(),
                    ],
                ),
            );

        $this->invalidator->invalidateLayoutBlocks([$uuid1->toString(), $uuid2->toString()]);
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
        $this->clientStub
            ->method('commit')
            ->willReturn(true);

        self::assertTrue($this->invalidator->commit());
    }
}
