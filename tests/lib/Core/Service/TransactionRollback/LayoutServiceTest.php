<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\ZoneMappings;
use Netgen\Layouts\Core\Service\LayoutService;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\Layouts\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutService::class)]
final class LayoutServiceTest extends TestCase
{
    public function testLinkZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturnOnConsecutiveCalls(
                PersistenceLayout::fromArray(['isShared' => false]),
                PersistenceLayout::fromArray(['isShared' => true]),
            );

        $this->layoutHandler
            ->method('loadZone')
            ->willReturnOnConsecutiveCalls(
                PersistenceZone::fromArray(['layoutId' => 1]),
                PersistenceZone::fromArray(['layoutId' => 2]),
            );

        $this->layoutHandler
            ->method('updateZone')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->linkZone(
            Zone::fromArray(['layoutId' => Uuid::v7(), 'identifier' => 'right', 'status' => Status::Draft]),
            Zone::fromArray(['layoutId' => Uuid::v7(), 'identifier' => 'left', 'status' => Status::Published]),
        );
    }

    public function testUnlinkZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadZone')
            ->willReturn(new PersistenceZone());

        $this->layoutHandler
            ->method('updateZone')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->unlinkZone(Zone::fromArray(['layoutId' => Uuid::v7(), 'identifier' => 'right', 'status' => Status::Draft]));
    }

    public function testCreateLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('layoutNameExists')
            ->willReturn(false);

        $this->layoutHandler
            ->method('createLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->name = 'Name';
        $layoutCreateStruct->mainLocale = 'en';
        $layoutCreateStruct->layoutType = LayoutType::fromArray(['identifier' => 'layout_type']);

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    public function testAddTranslation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(
                PersistenceLayout::fromArray(
                    [
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ],
                ),
            );

        $this->layoutHandler
            ->method('createLayoutTranslation')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->addTranslation(Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]), 'hr', 'en');
    }

    public function testRemoveTranslation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(
                PersistenceLayout::fromArray(
                    [
                        'mainLocale' => 'en',
                        'availableLocales' => ['en', 'hr'],
                    ],
                ),
            );

        $this->layoutHandler
            ->method('deleteLayoutTranslation')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->removeTranslation(Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]), 'hr');
    }

    public function testUpdateLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42, 'status' => PersistenceStatus::Draft]));

        $this->layoutHandler
            ->method('layoutNameExists')
            ->willReturn(false);

        $this->layoutHandler
            ->method('updateLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $layoutUpdateStruct = new LayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout(
            Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]),
            $layoutUpdateStruct,
        );
    }

    public function testCopyLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('layoutNameExists')
            ->willReturn(false);

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(new PersistenceLayout());

        $this->layoutHandler
            ->method('copyLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $layoutCopyStruct = new LayoutCopyStruct();
        $layoutCopyStruct->name = 'Name';

        $this->layoutService->copyLayout(
            Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]),
            $layoutCopyStruct,
        );
    }

    public function testChangeLayoutType(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(new PersistenceLayout());

        $this->layoutHandler
            ->method('loadLayoutZones')
            ->willReturn([]);

        $this->layoutHandler
            ->method('changeLayoutType')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->changeLayoutType(
            Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]),
            LayoutType::fromArray(['identifier' => 'test_layout_1']),
            new ZoneMappings(),
        );
    }

    public function testCreateDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42]));

        $this->layoutHandler
            ->method('layoutExists')
            ->willReturn(false);

        $this->layoutHandler
            ->method('deleteLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createDraft(Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Published]));
    }

    public function testDiscardDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42]));

        $this->layoutHandler
            ->method('deleteLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->discardDraft(Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]));
    }

    public function testPublishLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42]));

        $this->layoutHandler
            ->method('deleteLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->publishLayout(Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]));
    }

    public function testRestoreFromArchive(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42, 'status' => PersistenceStatus::Archived]));

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42, 'status' => PersistenceStatus::Published]));

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42, 'status' => PersistenceStatus::Draft]));

        $this->layoutHandler
            ->method('deleteLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->restoreFromArchive(Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Archived]));
    }

    public function testDeleteLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['id' => 42]));

        $this->layoutHandler
            ->method('deleteLayout')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->deleteLayout(Layout::fromArray(['id' => Uuid::v7(), 'status' => Status::Draft]));
    }
}
