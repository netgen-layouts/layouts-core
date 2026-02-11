<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache\Layout;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\HttpCache\Layout\IdProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(IdProvider::class)]
final class IdProviderTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private IdProvider $idProvider;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        $this->idProvider = new IdProvider($this->layoutServiceStub);
    }

    public function testProvideIds(): void
    {
        $uuid = Uuid::v7();

        $this->layoutServiceStub
            ->method('loadLayout')
            ->willReturn(
                Layout::fromArray(
                    [
                        'id' => $uuid,
                        'isShared' => false,
                    ],
                ),
            );

        $providedIds = $this->idProvider->provideIds($uuid->toString());

        self::assertSame([$uuid->toString()], $providedIds);
    }

    public function testProvideIdsWithNonExistingLayout(): void
    {
        $uuid = Uuid::v7();

        $this->layoutServiceStub
            ->method('loadLayout')
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        $providedIds = $this->idProvider->provideIds($uuid->toString());

        self::assertSame([$uuid->toString()], $providedIds);
    }

    public function testProvideIdsWithSharedLayout(): void
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();
        $uuid3 = Uuid::v7();

        $sharedLayout = Layout::fromArray(
            [
                'id' => $uuid1,
                'isShared' => true,
            ],
        );

        $this->layoutServiceStub
            ->method('loadLayout')
            ->willReturn($sharedLayout);

        $this->layoutServiceStub
            ->method('loadRelatedLayouts')
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
