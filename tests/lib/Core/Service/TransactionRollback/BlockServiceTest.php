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
use Netgen\BlockManager\Core\Validator\LayoutValidator;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;

final class BlockServiceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(PersistenceLayout::fromArray(['availableLocales' => ['en']])));

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('createBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayoutDraft')
            ->will(
                self::returnValue(
                    Layout::fromArray(
                        [
                            'availableLocales' => ['en'],
                            'layoutType' => new LayoutType(),
                        ]
                    )
                )
            );

        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadZone')
            ->will(self::returnValue(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('createBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue($persistenceBlock));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('updateBlockTranslation')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects(self::at(2))
            ->method('copyBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayoutDraft')
            ->will(self::returnValue(Layout::fromArray(['layoutType' => new LayoutType()])));

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadZone')
            ->will(self::returnValue(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects(self::at(2))
            ->method('copyBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(PersistenceBlock::fromArray(['parentId' => 1, 'placeholder' => 'main'])));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('loadBlock')
            ->will(self::returnValue(PersistenceBlock::fromArray(['id' => 1])));

        $this->blockHandlerMock
            ->expects(self::at(2))
            ->method('moveBlockToPosition')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayoutDraft')
            ->will(self::returnValue(Layout::fromArray(['layoutType' => new LayoutType()])));

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(PersistenceBlock::fromArray(['parentId' => 1, 'placeholder' => 'root'])));

        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadZone')
            ->will(self::returnValue(PersistenceZone::fromArray(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('loadBlock')
            ->will(self::returnValue(PersistenceBlock::fromArray(['id' => 1])));

        $this->blockHandlerMock
            ->expects(self::at(2))
            ->method('moveBlockToPosition')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('restoreBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(PersistenceBlock::fromArray(['isTranslatable' => false, 'parentId' => 42])));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('loadBlock')
            ->will(self::returnValue(PersistenceBlock::fromArray(['isTranslatable' => true, 'depth' => 1])));

        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->blockHandlerMock
            ->expects(self::at(2))
            ->method('updateBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(PersistenceBlock::fromArray(['isTranslatable' => true])));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('updateBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
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

        $this->blockHandlerMock
            ->expects(self::at(0))
            ->method('loadBlock')
            ->will(self::returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects(self::at(1))
            ->method('deleteBlock')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->blockService->deleteBlock(Block::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * Creates a layout service under test.
     */
    protected function createLayoutService(?LayoutValidator $validator = null): LayoutService
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        return $this->layoutServiceMock;
    }
}
