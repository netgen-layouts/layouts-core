<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler;
use Exception;

class BlockServiceTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

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
    protected $blockValidatorMock;

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

        $this->blockHandlerMock = $this->getMockBuilder(BlockHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutHandlerMock = $this->getMockBuilder(LayoutHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getBlockHandler')
            ->will($this->returnValue($this->blockHandlerMock));

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getLayoutHandler')
            ->will($this->returnValue($this->layoutHandlerMock));

        $this->blockValidatorMock = $this->getMockBuilder(BlockValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockService = $this->createBlockService($this->blockValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Exception
     */
    public function testCreateBlock()
    {
        $this->layoutHandlerMock
            ->expects($this->once())
            ->method('zoneExists')
            ->will($this->returnValue(true));

        $this->blockHandlerMock
            ->expects($this->once())
            ->method('createBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->createBlock(
            new BlockCreateStruct(),
            new Layout(array('status' => Layout::STATUS_DRAFT)),
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
            ->expects($this->once())
            ->method('updateBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->updateBlock(
            new Block(array('status' => Layout::STATUS_DRAFT)),
            new BlockUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Exception
     */
    public function testCopyBlock()
    {
        $this->blockHandlerMock
            ->expects($this->once())
            ->method('copyBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->copyBlock(new Block(array('status' => Layout::STATUS_DRAFT)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Exception
     */
    public function testMoveBlock()
    {
        $this->blockHandlerMock
            ->expects($this->once())
            ->method('moveBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->moveBlock(new Block(array('status' => Layout::STATUS_DRAFT)), 0);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Exception
     */
    public function testDeleteBlock()
    {
        $this->blockHandlerMock
            ->expects($this->once())
            ->method('deleteBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->deleteBlock(new Block(array('status' => Layout::STATUS_DRAFT)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::addCollectionToBlock
     * @expectedException \Exception
     */
    public function testAddCollectionToBlock()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('collectionExists')
            ->will($this->returnValue(false));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('collectionIdentifierExists')
            ->will($this->returnValue(false));

        $this->blockHandlerMock
            ->expects($this->at(2))
            ->method('addCollectionToBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->addCollectionToBlock(
            new Block(),
            new Collection(),
            'identifier'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::removeCollectionFromBlock
     * @expectedException \Exception
     */
    public function testRemoveCollectionFromBlock()
    {
        $this->blockHandlerMock
            ->expects($this->at(0))
            ->method('collectionExists')
            ->will($this->returnValue(true));

        $this->blockHandlerMock
            ->expects($this->at(1))
            ->method('removeCollectionFromBlock')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->blockService->removeCollectionFromBlock(
            new Block(),
            new Collection()
        );
    }
}
