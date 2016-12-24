<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\Page\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Page\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference as PersistenceCollectionReference;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;

class BlockServiceTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

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
     * @expectedException \Exception
     */
    public function testCreateBlock()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('type' => '4_zones_b'))));

        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('createBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->createBlock(
            new BlockCreateStruct(array('definition' => new BlockDefinition('blockDef'))),
            new Zone(array('published' => false))
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
            ->will($this->returnValue(new PersistenceBlock(array('parameters' => array()))));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('updateBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->updateBlock(
            new Block(
                array(
                    'published' => false,
                    'definition' => new BlockDefinition('block_definition'),
                )
            ),
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
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('type' => '4_zones_b'))));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock()));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('copyBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlock(
            new Block(array('published' => false)),
            new Zone(array('published' => false))
        );
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
            ->will($this->returnValue(new PersistenceBlock(array('parentId' => 1, 'placeholder' => 'root'))));

        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('type' => '4_zones_b'))));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('loadBlock')
            ->will($this->returnValue(new PersistenceBlock(array('id' => 1))));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('moveBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(
            new Block(array('published' => false)),
            new Zone(array('published' => false)),
            0
        );
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

        $this->blockService->restoreBlock(new Block(array('published' => false)));
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

        $this->blockService->deleteBlock(new Block(array('published' => false)));
    }
}
