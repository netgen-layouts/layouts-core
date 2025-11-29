<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\API\Values\Config\ConfigList;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Core\Service\BlockService;
use Netgen\Layouts\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\Layouts\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\Layouts\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use Ramsey\Uuid\Uuid;

#[CoversClass(BlockService::class)]
final class BlockServiceTest extends TestCase
{
    public function testCreateBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['availableLocales' => ['en']]));

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['status' => PersistenceStatus::Published]));

        $this->blockHandler
            ->method('createBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $createStruct = $this->blockService->newBlockCreateStruct(
            BlockDefinition::fromArray(
                [
                    'identifier' => 'definition',
                    'isTranslatable' => false,
                    'configProvider' => ConfigProvider::fromShortConfig(['large' => ['standard']]),
                ],
            ),
        );

        $this->blockService->createBlock(
            $createStruct,
            Block::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'layoutId' => Uuid::uuid4(),
                    'status' => Status::Draft,
                    'definition' => ContainerDefinition::fromArray(
                        [
                            'handler' => new ContainerDefinitionHandler([], ['main']),
                        ],
                    ),
                ],
            ),
            'main',
        );
    }

    public function testCreateBlockInZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['type' => '4_zones_a']));

        $this->layoutHandler
            ->method('loadZone')
            ->willReturn(PersistenceZone::fromArray(['status' => PersistenceStatus::Draft, 'identifier' => 'left', 'rootBlockId' => 42]));

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['status' => PersistenceStatus::Published]));

        $this->blockHandler
            ->method('createBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $createStruct = $this->blockService->newBlockCreateStruct(
            BlockDefinition::fromArray(
                [
                    'identifier' => 'definition',
                    'isTranslatable' => false,
                    'configProvider' => ConfigProvider::fromShortConfig(['default' => ['standard']]),
                ],
            ),
        );

        $this->blockService->createBlockInZone(
            $createStruct,
            Zone::fromArray(['layoutId' => Uuid::uuid4(), 'status' => Status::Draft, 'identifier' => 'left']),
        );
    }

    public function testUpdateBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $persistenceBlock = PersistenceBlock::fromArray(
            [
                'config' => [],
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => ['en' => []],
            ],
        );

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn($persistenceBlock);

        $this->blockHandler
            ->method('updateBlockTranslation')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $struct = new BlockUpdateStruct();
        $struct->locale = 'en';

        $this->blockService->updateBlock(
            Block::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Draft,
                    'definition' => new BlockDefinition(),
                    'configs' => new ConfigList(),
                ],
            ),
            $struct,
        );
    }

    public function testCopyBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['layoutId' => 42]));

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['layoutId' => 42]));

        $this->blockHandler
            ->method('copyBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlock(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft, 'definition' => new BlockDefinition()]),
            Block::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Draft,
                    'definition' => ContainerDefinition::fromArray(
                        [
                            'handler' => new ContainerDefinitionHandler([], ['main']),
                        ],
                    ),
                ],
            ),
            'main',
        );
    }

    public function testCopyBlockToZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['layoutId' => 42]));

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['type' => '4_zones_a']));

        $this->layoutHandler
            ->method('loadZone')
            ->willReturn(PersistenceZone::fromArray(['status' => PersistenceStatus::Draft, 'identifier' => 'left', 'layoutId' => 42, 'rootBlockId' => 24]));

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['layoutId' => 42]));

        $this->blockHandler
            ->method('copyBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlockToZone(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft, 'definition' => new BlockDefinition()]),
            Zone::fromArray(['layoutId' => Uuid::uuid4(), 'status' => Status::Draft, 'identifier' => 'left']),
        );
    }

    public function testMoveBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturnOnConsecutiveCalls(
                PersistenceBlock::fromArray(['parentId' => 1, 'placeholder' => 'main', 'layoutId' => 42]),
                PersistenceBlock::fromArray(['id' => 1, 'layoutId' => 42]),
            );

        $this->blockHandler
            ->method('moveBlockToPosition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft, 'definition' => new BlockDefinition()]),
            Block::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Draft,
                    'definition' => ContainerDefinition::fromArray(
                        [
                            'handler' => new ContainerDefinitionHandler([], ['main']),
                        ],
                    ),
                ],
            ),
            'main',
            0,
        );
    }

    public function testMoveBlockToZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturnOnConsecutiveCalls(
                PersistenceBlock::fromArray(['parentId' => 1, 'placeholder' => 'root', 'layoutId' => 42]),
                PersistenceBlock::fromArray(['id' => 1, 'layoutId' => 42]),
            );

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['type' => '4_zones_a']));

        $this->layoutHandler
            ->method('loadZone')
            ->willReturn(PersistenceZone::fromArray(['status' => PersistenceStatus::Draft, 'identifier' => 'left', 'layoutId' => 42, 'rootBlockId' => 24]));

        $this->blockHandler
            ->method('moveBlockToPosition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlockToZone(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft, 'definition' => new BlockDefinition()]),
            Zone::fromArray(['layoutId' => Uuid::uuid4(), 'status' => Status::Draft, 'identifier' => 'left']),
            0,
        );
    }

    public function testRestoreBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['layoutId' => 42]));

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(new PersistenceLayout());

        $this->blockHandler
            ->method('restoreBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->restoreBlock(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    public function testEnableTranslations(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturnOnConsecutiveCalls(
                PersistenceBlock::fromArray(['isTranslatable' => false, 'parentId' => 42, 'layoutId' => 24]),
                PersistenceBlock::fromArray(['isTranslatable' => true, 'depth' => 1]),
            );

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(new PersistenceLayout());

        $this->blockHandler
            ->method('updateBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->enableTranslations(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    public function testDisableTranslations(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['isTranslatable' => true]));

        $this->blockHandler
            ->method('updateBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->disableTranslations(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    public function testDeleteBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->blockHandler
            ->method('deleteBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->deleteBlock(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    protected function createLayoutService(): LayoutService
    {
        return $this->createMock(LayoutService::class);
    }
}
