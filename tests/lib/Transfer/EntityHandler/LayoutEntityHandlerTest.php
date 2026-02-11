<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\EntityHandler;

use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Transfer\EntityHandler\LayoutEntityHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

use function sprintf;

#[CoversClass(LayoutEntityHandler::class)]
final class LayoutEntityHandlerTest extends TestCase
{
    private MockObject&LayoutService $layoutServiceMock;

    private LayoutEntityHandler $entityHandler;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->entityHandler = new LayoutEntityHandler(
            self::createStub(BlockService::class),
            self::createStub(CollectionService::class),
            $this->layoutServiceMock,
            new BlockDefinitionRegistry([]),
            new LayoutTypeRegistry([]),
            new ItemDefinitionRegistry([]),
            new QueryTypeRegistry([]),
            self::createStub(CmsItemLoaderInterface::class),
        );
    }

    public function testLoadEntity(): void
    {
        $uuid = Uuid::v7();

        $layout = Layout::fromArray(['id' => $uuid]);

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with(self::identicalTo($uuid))
            ->willReturn($layout);

        self::assertSame($layout, $this->entityHandler->loadEntity($uuid));
    }

    public function testLoadEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v7();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find layout with identifier "%s"', $uuid->toString()));

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        $this->entityHandler->loadEntity($uuid);
    }

    public function testEntityExists(): void
    {
        $uuid = Uuid::v7();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('layoutExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(true);

        self::assertTrue($this->entityHandler->entityExists($uuid));
    }

    public function testEntityExistsReturnsFalse(): void
    {
        $uuid = Uuid::v7();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('layoutExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(false);

        self::assertFalse($this->entityHandler->entityExists($uuid));
    }

    public function testDeleteEntity(): void
    {
        $uuid = Uuid::v7();

        $layout = Layout::fromArray(['id' => $uuid]);

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with(self::identicalTo($uuid))
            ->willReturn($layout);

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('deleteLayout')
            ->with(self::identicalTo($layout));

        $this->entityHandler->deleteEntity($uuid);
    }

    public function testDeleteEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v7();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find layout with identifier "%s"', $uuid->toString()));

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        $this->entityHandler->deleteEntity($uuid);
    }
}
