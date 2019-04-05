<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;

/**
 * @property \PHPUnit\Framework\MockObject\MockObject $layoutService
 */
final class BlockServiceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->willReturn(PersistenceLayout::fromArray(['availableLocales' => ['en']]));

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->blockHandler
            ->expects(self::at(1))
            ->method('createBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->createBlock(
            new BlockCreateStruct(BlockDefinition::fromArray(['identifier' => 'definition'])),
            Block::fromArray(
                [
                    'status' => Value::STATUS_DRAFT,
                    'definition' => ContainerDefinition::fromArray(
                        [
                            'handler' => new ContainerDefinitionHandler([], ['main']),
                        ]
                    ),
                ]
            ),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutService
            ->expects(self::at(0))
            ->method('loadLayoutDraft')
            ->willReturn(
                Layout::fromArray(
                    [
                        'availableLocales' => ['en'],
                        'layoutType' => new LayoutType(),
                    ]
                )
            );

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadZone')
            ->willReturn(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right']));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('loadLayout')
            ->willReturn(new PersistenceLayout());

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->blockHandler
            ->expects(self::at(1))
            ->method('createBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->createBlockInZone(
            new BlockCreateStruct(BlockDefinition::fromArray(['identifier' => 'definition'])),
            Zone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
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
            ]
        );

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn($persistenceBlock);

        $this->blockHandler
            ->expects(self::at(1))
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
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new BlockDefinition(),
                    'configs' => [],
                ]
            ),
            $struct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->blockHandler
            ->expects(self::at(1))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->blockHandler
            ->expects(self::at(2))
            ->method('copyBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlock(
            Block::fromArray(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Block::fromArray(
                [
                    'status' => Value::STATUS_DRAFT,
                    'definition' => ContainerDefinition::fromArray(
                        [
                            'handler' => new ContainerDefinitionHandler([], ['main']),
                        ]
                    ),
                ]
            ),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutService
            ->expects(self::at(0))
            ->method('loadLayoutDraft')
            ->willReturn(Layout::fromArray(['layoutType' => new LayoutType()]));

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadZone')
            ->willReturn(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right']));

        $this->blockHandler
            ->expects(self::at(1))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->blockHandler
            ->expects(self::at(2))
            ->method('copyBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlockToZone(
            Block::fromArray(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Zone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['parentId' => 1, 'placeholder' => 'main']));

        $this->blockHandler
            ->expects(self::at(1))
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['id' => 1]));

        $this->blockHandler
            ->expects(self::at(2))
            ->method('moveBlockToPosition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(
            Block::fromArray(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Block::fromArray(
                [
                    'status' => Value::STATUS_DRAFT,
                    'definition' => ContainerDefinition::fromArray(
                        [
                            'handler' => new ContainerDefinitionHandler([], ['main']),
                        ]
                    ),
                ]
            ),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutService
            ->expects(self::at(0))
            ->method('loadLayoutDraft')
            ->willReturn(Layout::fromArray(['layoutType' => new LayoutType()]));

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['parentId' => 1, 'placeholder' => 'root']));

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadZone')
            ->willReturn(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right']));

        $this->blockHandler
            ->expects(self::at(1))
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['id' => 1]));

        $this->blockHandler
            ->expects(self::at(2))
            ->method('moveBlockToPosition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlockToZone(
            Block::fromArray(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            Zone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right']),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->willReturn(new PersistenceLayout());

        $this->blockHandler
            ->expects(self::at(1))
            ->method('restoreBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->restoreBlock(Block::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     */
    public function testEnableTranslations(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['isTranslatable' => false, 'parentId' => 42]));

        $this->blockHandler
            ->expects(self::at(1))
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['isTranslatable' => true, 'depth' => 1]));

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->willReturn(new PersistenceLayout());

        $this->blockHandler
            ->expects(self::at(2))
            ->method('updateBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->enableTranslations(Block::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     */
    public function testDisableTranslations(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(PersistenceBlock::fromArray(['isTranslatable' => true]));

        $this->blockHandler
            ->expects(self::at(1))
            ->method('updateBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->disableTranslations(Block::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     */
    public function testDeleteBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->blockHandler
            ->expects(self::at(0))
            ->method('loadBlock')
            ->willReturn(new PersistenceBlock());

        $this->blockHandler
            ->expects(self::at(1))
            ->method('deleteBlock')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->deleteBlock(Block::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    protected function createLayoutService(): LayoutService
    {
        return $this->createMock(LayoutService::class);
    }
}
