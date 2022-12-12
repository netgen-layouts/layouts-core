<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\Layouts\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\Layouts\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Ramsey\Uuid\Uuid;

/**
 * @property \PHPUnit\Framework\MockObject\MockObject $layoutService
 */
final class BlockServiceTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['availableLocales' => ['en']]));

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['status' => Value::STATUS_PUBLISHED]));

        $this->blockHandler
            ->method('createBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
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
                    'status' => Value::STATUS_DRAFT,
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

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['type' => '4_zones_a']));

        $this->layoutHandler
            ->method('loadZone')
            ->willReturn(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'left', 'rootBlockId' => 42]));

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['status' => Value::STATUS_PUBLISHED]));

        $this->blockHandler
            ->method('createBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
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
            Zone::fromArray(['layoutId' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'identifier' => 'left']),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     */
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
            ->expects(self::once())
            ->method('rollbackTransaction');

        $struct = new BlockUpdateStruct();
        $struct->locale = 'en';

        $this->blockService->updateBlock(
            Block::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new BlockDefinition(),
                    'configs' => [],
                ],
            ),
            $struct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
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
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlock(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Block::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Value::STATUS_DRAFT,
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

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
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
            ->willReturn(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'left', 'layoutId' => 42, 'rootBlockId' => 24]));

        $this->blockHandler
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['layoutId' => 42]));

        $this->blockHandler
            ->method('copyBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlockToZone(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Zone::fromArray(['layoutId' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'identifier' => 'left']),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
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
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Block::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Value::STATUS_DRAFT,
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

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
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
            ->willReturn(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'left', 'layoutId' => 42, 'rootBlockId' => 24]));

        $this->blockHandler
            ->method('moveBlockToPosition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlockToZone(
            Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Zone::fromArray(['layoutId' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'identifier' => 'left']),
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::restoreBlock
     */
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
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->restoreBlock(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::enableTranslations
     */
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
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->enableTranslations(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::disableTranslations
     */
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
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->disableTranslations(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::deleteBlock
     */
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
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->deleteBlock(Block::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    protected function createLayoutService(): LayoutService
    {
        return $this->createMock(LayoutService::class);
    }
}
