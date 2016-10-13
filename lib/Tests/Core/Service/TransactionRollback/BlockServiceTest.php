<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\BlockDraft;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference as PersistenceCollectionReference;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Core\Values\Page\LayoutDraft;
use Netgen\BlockManager\Persistence\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler;
use Exception;

class BlockServiceTest extends TransactionRollbackTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockValidatorMock;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        $this->blockHandlerMock = $this->createMock(BlockHandler::class);
        $this->layoutHandlerMock = $this->createMock(LayoutHandler::class);
        $this->collectionHandlerMock = $this->createMock(CollectionHandler::class);

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getBlockHandler')
            ->will($this->returnValue($this->blockHandlerMock));

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getLayoutHandler')
            ->will($this->returnValue($this->layoutHandlerMock));

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getCollectionHandler')
            ->will($this->returnValue($this->collectionHandlerMock));

        $this->blockValidatorMock = $this->createMock(BlockValidator::class);

        $this->blockService = $this->createBlockService(
            $this->blockValidatorMock,
            new LayoutTypeRegistry(),
            new BlockDefinitionRegistry()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Exception
     */
    public function testCreateBlock()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('zoneExists')
            ->will($this->returnValue(true));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('createBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->createBlock(
            new BlockCreateStruct(),
            new LayoutDraft(),
            'zone'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Exception
     */
    public function testUpdateBlock()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('updateBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->updateBlock(
            new BlockDraft(),
            new BlockUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateCollectionReference
     * @expectedException \Exception
     */
    public function testUpdateCollectionReference()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadCollectionReference')
            ->will($this->returnValue(new PersistenceCollectionReference()));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('updateCollectionReference')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->updateCollectionReference(
            new CollectionReference(
                array(
                    'block' => new Block(),
                )
            ),
            new Collection()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Exception
     */
    public function testCopyBlock()
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
            ->method('copyBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlock(new BlockDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Exception
     */
    public function testMoveBlock()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('moveBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(new BlockDraft(), 0);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     * @expectedException \Exception
     */
    public function testRestoreBlock()
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
            ->method('updateBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->restoreBlock(new BlockDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Exception
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
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->deleteBlock(new BlockDraft());
    }
}
