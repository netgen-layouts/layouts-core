<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache\Layout;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\HttpCache\Layout\IdProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class IdProviderTest extends TestCase
{
    private MockObject $layoutServiceMock;

    private IdProvider $idProvider;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->idProvider = new IdProvider($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Layout\IdProvider::__construct
     * @covers \Netgen\Layouts\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIds(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn(
                Layout::fromArray(
                    [
                        'id' => $uuid,
                        'shared' => false,
                    ],
                ),
            );

        $providedIds = $this->idProvider->provideIds($uuid->toString());

        self::assertSame([$uuid->toString()], $providedIds);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithNonExistingLayout(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        $providedIds = $this->idProvider->provideIds($uuid->toString());

        self::assertSame([$uuid->toString()], $providedIds);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithSharedLayout(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();
        $uuid3 = Uuid::uuid4();

        $sharedLayout = Layout::fromArray(
            [
                'id' => $uuid1,
                'shared' => true,
            ],
        );

        $this->layoutServiceMock
            ->method('loadLayout')
            ->with(self::equalTo($uuid1))
            ->willReturn($sharedLayout);

        $this->layoutServiceMock
            ->method('loadRelatedLayouts')
            ->with(self::identicalTo($sharedLayout))
            ->willReturn(
                new LayoutList(
                    [
                        Layout::fromArray(
                            [
                                'id' => $uuid2,
                            ],
                        ),
                        Layout::fromArray(
                            [
                                'id' => $uuid3,
                            ],
                        ),
                    ],
                ),
            );

        $providedIds = $this->idProvider->provideIds($uuid1->toString());

        self::assertSame([$uuid1->toString(), $uuid2->toString(), $uuid3->toString()], $providedIds);
    }
}
