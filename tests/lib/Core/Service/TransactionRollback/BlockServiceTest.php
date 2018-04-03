<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;

final class BlockServiceTest extends ServiceTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
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
    public function testCreateBlock()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('availableLocales' => array('en')))));

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
                array(
                    'definition' => new BlockDefinition('blockDef'),
                )
            ),
            new Block(
                array(
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new ContainerDefinition(
                        'blockDef',
                        array(),
                        new ContainerDefinitionHandler(array(), array('main'))
                    ),
                )
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
    public function testCreateBlockInZone()
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayoutDraft')
            ->will(
                $this->returnValue(
                    new Layout(
                        array(
                            'availableLocales' => array('en'),
                            'layoutType' => new LayoutType(),
                        )
                    )
                )
            );

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

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
                array(
                    'definition' => new BlockDefinition('blockDef'),
                )
            ),
            new Zone(array('status' => Value::STATUS_DRAFT, 'identifier' => 'right'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateBlock()
    {
        $persistenceBlock = new PersistenceBlock(
            array(
                'config' => array(),
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'parameters' => array('en' => array()),
            )
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
                array(
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new BlockDefinition('block_definition'),
                    'configs' => array(),
                )
            ),
            new BlockUpdateStruct(array('locale' => 'en'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCopyBlock()
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
            new Block(array('status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition('blockDef'))),
            new Block(
                array(
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new ContainerDefinition(
                        'blockDef',
                        array(),
                        new ContainerDefinitionHandler(array(), array('main'))
                    ),
                )
            ),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCopyBlockToZone()
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayoutDraft')
            ->will($this->returnValue(new Layout(array('layoutType' => new LayoutType()))));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

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
            new Block(array('status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition('blockDef'))),
            new Zone(array('status' => Value::STATUS_DRAFT, 'identifier' => 'right'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testMoveBlock()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('parentId' => 1, 'placeholder' => 'main'))));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('id' => 1))));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('moveBlockToPosition')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(
            new Block(array('status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition('blockDef'))),
            new Block(
                array(
                    'status' => Value::STATUS_DRAFT,
                    'definition' => new ContainerDefinition(
                        'blockDef',
                        array(),
                        new ContainerDefinitionHandler(array(), array('main'))
                    ),
                )
            ),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testMoveBlockToZone()
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayoutDraft')
            ->will($this->returnValue(new Layout(array('layoutType' => new LayoutType()))));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('parentId' => 1, 'placeholder' => 'root'))));

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('id' => 1))));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('moveBlockToPosition')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlockToZone(
            new Block(array('status' => Value::STATUS_DRAFT, 'definition' => new BlockDefinition('blockDef'))),
            new Zone(array('status' => Value::STATUS_DRAFT, 'identifier' => 'right')),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRestoreBlock()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('restoreBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->restoreBlock(new Block(array('status' => Value::STATUS_DRAFT)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testEnableTranslations()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('isTranslatable' => false))));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('isTranslatable' => true, 'depth' => 1))));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('updateBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->enableTranslations(new Block(array('status' => Value::STATUS_DRAFT)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDisableTranslations()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('isTranslatable' => true))));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('updateBlock')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->disableTranslations(new Block(array('status' => Value::STATUS_DRAFT)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDeleteBlock()
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

        $this->blockService->deleteBlock(new Block(array('status' => Value::STATUS_DRAFT)));
    }

    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(LayoutValidator $validator = null)
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        return $this->layoutServiceMock;
    }
}
