<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;

final class BlockServiceTest extends ServiceTestCase
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
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCreateBlock(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(['availableLocales' => ['en']])));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('createBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->createBlock(
            new BlockCreateStruct(
                [
                    'definition' => new BlockDefinition(['identifier' => 'definition']),
                ]
            ),
            new Block(
                [
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new ContainerDefinition(
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
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCreateBlockInZone(): void
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayoutDraft')
            ->will(
                $this->returnValue(
                    new Layout(
                        [
                            'availableLocales' => ['en'],
                            'layoutType' => new LayoutType(),
                        ]
                    )
                )
            );

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('createBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->createBlockInZone(
            new BlockCreateStruct(
                [
                    'definition' => new BlockDefinition(['identifier' => 'definition']),
                ]
            ),
            new Zone(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateBlock(): void
    {
        $persistenceBlock = new PersistenceBlock(
            [
                'config' => [],
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => ['en' => []],
            ]
        );

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue($persistenceBlock));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('updateBlockTranslation')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->updateBlock(
            new Block(
                [
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new BlockDefinition(),
                    'configs' => [],
                ]
            ),
            new BlockUpdateStruct(['locale' => 'en'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCopyBlock(): void
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('copyBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlock(
            new Block(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            new Block(
                [
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new ContainerDefinition(
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
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCopyBlockToZone(): void
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayoutDraft')
            ->will($this->returnValue(new Layout(['layoutType' => new LayoutType()])));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('copyBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlockToZone(
            new Block(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            new Zone(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testMoveBlock(): void
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(['parentId' => 1, 'placeholder' => 'main'])));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(['id' => 1])));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('moveBlockToPosition')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(
            new Block(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            new Block(
                [
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new ContainerDefinition(
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
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testMoveBlockToZone(): void
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayoutDraft')
            ->will($this->returnValue(new Layout(['layoutType' => new LayoutType()])));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(['parentId' => 1, 'placeholder' => 'root'])));

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone(['status' => Value::STATUS_DRAFT, 'identifier' => 'right'])));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(['id' => 1])));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('moveBlockToPosition')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlockToZone(
            new Block(['status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition()]),
            new Zone(['status' => Value::STATUS_DRAFT, 'identifier' => 'right']),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRestoreBlock(): void
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('restoreBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->restoreBlock(new Block(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testEnableTranslations(): void
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(['isTranslatable' => false, 'parentId' => 42])));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(['isTranslatable' => true, 'depth' => 1])));

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('updateBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->enableTranslations(new Block(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDisableTranslations(): void
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(['isTranslatable' => true])));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('updateBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->disableTranslations(new Block(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDeleteBlock(): void
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('deleteBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->deleteBlock(new Block(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * Creates a layout service under test.
     */
    protected function createLayoutService(LayoutValidator $validator = null): LayoutService
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        return $this->layoutServiceMock;
    }
}
