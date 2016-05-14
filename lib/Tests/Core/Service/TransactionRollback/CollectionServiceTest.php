<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
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

        $this->collectionHandlerMock = $this->getMockBuilder(CollectionHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getCollectionHandler')
            ->will($this->returnValue($this->collectionHandlerMock));

        $this->collectionValidatorMock = $this->getMockBuilder(CollectionValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionService = $this->createCollectionService($this->collectionValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createNamedCollection
     * @expectedException \Exception
     */
    public function testCreateNamedCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('namedCollectionExists')
            ->will($this->returnValue(false));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('createCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->createNamedCollection(new CollectionCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateNamedCollection
     * @expectedException \Exception
     */
    public function testUpdateNamedCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_NAMED, 'status' => Collection::STATUS_DRAFT)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateNamedCollection')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->updateNamedCollection(
            new Collection(),
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollectionStatus
     * @expectedException \Exception
     */
    public function testCreateCollectionStatus()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection()
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('collectionExists')
            ->will($this->returnValue(false));

        $this->collectionHandlerMock
            ->expects($this->at(2))
            ->method('createCollectionStatus')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->createCollectionStatus(new Collection(), Collection::STATUS_ARCHIVED);
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
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('id' => 42, 'status' => Collection::STATUS_PUBLISHED)
                    )
                )
            );

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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     * @expectedException \Exception
     */
    public function testPublishCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('status' => Collection::STATUS_DRAFT)
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

        $this->collectionService->publishCollection(new Collection());
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
                        array('type' => Collection::TYPE_MANUAL, 'status' => Collection::STATUS_DRAFT)
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
            new Collection(),
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
            ->will(
                $this->returnValue(
                    new PersistenceItem(
                        array('status' => Collection::STATUS_DRAFT)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->collectionHandlerMock
            ->expects($this->at(2))
            ->method('itemPositionExists')
            ->will($this->returnValue(false));

        $this->collectionHandlerMock
            ->expects($this->at(3))
            ->method('moveItem')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->moveItem(new Item(), 0);
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
            ->will(
                $this->returnValue(
                    new PersistenceItem(
                        array('status' => Collection::STATUS_DRAFT)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteItem')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItem(new Item());
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
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('status' => Collection::STATUS_DRAFT)
                    )
                )
            );

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
            new Collection(array('status' => Collection::STATUS_DRAFT)),
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
            ->will(
                $this->returnValue(
                    new PersistenceQuery(
                        array('status' => Collection::STATUS_DRAFT)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateQuery')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->updateQuery(
            new Query(),
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
            ->will(
                $this->returnValue(
                    new PersistenceQuery(
                        array('status' => Collection::STATUS_DRAFT)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('moveQuery')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->moveQuery(new Query(), 0);
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
            ->will(
                $this->returnValue(
                    new PersistenceQuery(
                        array('status' => Collection::STATUS_DRAFT)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteQuery')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteQuery(new Query());
    }
}
