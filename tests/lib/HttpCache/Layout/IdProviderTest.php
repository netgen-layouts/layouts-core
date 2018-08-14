<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\HttpCache\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutList;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\HttpCache\Layout\IdProvider;
use PHPUnit\Framework\TestCase;

final class IdProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\IdProvider
     */
    private $idProvider;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->idProvider = new IdProvider($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::__construct
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIds(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->will(
                self::returnValue(
                    Layout::fromArray(
                        [
                            'id' => 42,
                            'shared' => false,
                        ]
                    )
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        self::assertSame([42], $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithNonExistingLayout(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->will(
                self::throwException(
                    new NotFoundException('layout', 42)
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        self::assertSame([42], $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithSharedLayout(): void
    {
        $sharedLayout = Layout::fromArray(
            [
                'id' => 42,
                'shared' => true,
            ]
        );

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($sharedLayout));

        $this->layoutServiceMock
            ->expects(self::at(1))
            ->method('loadRelatedLayouts')
            ->with(self::identicalTo($sharedLayout))
            ->will(
                self::returnValue(
                    new LayoutList(
                        [
                            Layout::fromArray(
                                [
                                    'id' => 43,
                                ]
                            ),
                            Layout::fromArray(
                                [
                                    'id' => 44,
                                ]
                            ),
                        ]
                    )
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        self::assertSame([42, 43, 44], $providedIds);
    }
}
