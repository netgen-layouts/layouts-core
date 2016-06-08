<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\CollectionDraft;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\ItemDraft;
use Netgen\BlockManager\Core\Values\Collection\QueryDraft;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\BlockManager\Core\Values\QueryCreateStruct;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler;
use Exception;

class CollectionServiceTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionValidatorMock;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        $this->collectionHandlerMock = $this->createMock(CollectionHandler::class);

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getCollectionHandler')
            ->will($this->returnValue($this->collectionHandlerMock));

        $this->collectionValidatorMock = $this->createMock(CollectionValidator::class);

        $this->collectionService = $this->createCollectionService($this->collectionValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     * @expectedException \Exception
     */
    public function testCreateCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('createCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->createCollection(new CollectionCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Exception
     */
    public function testUpdateCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_NAMED)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->updateCollection(
            new CollectionDraft(),
            new CollectionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     * @expectedException \Exception
     */
    public function testCopyCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_NAMED)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('copyCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->copyCollection(new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     * @expectedException \Exception
     */
    public function testCreateDraft()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('collectionExists')
            ->will($this->returnValue(false));

        $this->collectionHandlerMock
            ->expects($this->at(2))
            ->method('deleteCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->createDraft(new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::discardDraft
     * @expectedException \Exception
     */
    public function testDiscardDraft()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->discardDraft(new CollectionDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     * @expectedException \Exception
     */
    public function testPublishCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->publishCollection(new CollectionDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteCollection
     * @expectedException \Exception
     */
    public function testDeleteCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_NAMED)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteCollection(new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Exception
     */
    public function testAddItem()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_MANUAL)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('addItem')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->addItem(
            new CollectionDraft(),
            new ItemCreateStruct(array('type' => Item::TYPE_MANUAL))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Exception
     */
    public function testMoveItem()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->will($this->returnValue(new PersistenceItem()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->collectionHandlerMock
            ->expects($this->at(2))
            ->method('moveItem')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->moveItem(new ItemDraft(), 0);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     * @expectedException \Exception
     */
    public function testDeleteItem()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->will($this->returnValue(new PersistenceItem()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteItem')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItem(new ItemDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Exception
     */
    public function testAddQuery()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('queryIdentifierExists')
            ->will($this->returnValue(false));

        $this->collectionHandlerMock
            ->expects($this->at(2))
            ->method('addQuery')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->addQuery(
            new CollectionDraft(),
            new QueryCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Exception
     */
    public function testUpdateQuery()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadQuery')
            ->will($this->returnValue(new PersistenceQuery()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateQuery')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->updateQuery(
            new QueryDraft(),
            new QueryUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     * @expectedException \Exception
     */
    public function testMoveQuery()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadQuery')
            ->will($this->returnValue(new PersistenceQuery()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('moveQuery')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->moveQuery(new QueryDraft(), 0);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteQuery
     * @expectedException \Exception
     */
    public function testDeleteQuery()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadQuery')
            ->will($this->returnValue(new PersistenceQuery()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteQuery')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteQuery(new QueryDraft());
    }
}
