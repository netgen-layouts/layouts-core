<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;

class CollectionServiceTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCreateCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('createCollection')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->createCollection(new CollectionCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testChangeCollectionType()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_DYNAMIC)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateCollection')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->changeCollectionType(
            new Collection(array('published' => false)),
            Collection::TYPE_MANUAL
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCopyCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_DYNAMIC)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('copyCollection')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->copyCollection(new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->createDraft(new Collection(array('published' => true)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::discardDraft
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->discardDraft(new Collection(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->publishCollection(new Collection(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteCollection
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDeleteCollection()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will(
                $this->returnValue(
                    new PersistenceCollection(
                        array('type' => Collection::TYPE_DYNAMIC)
                    )
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteCollection')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteCollection(new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->addItem(
            new Collection(array('published' => false)),
            new ItemCreateStruct(array('type' => Item::TYPE_MANUAL))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testMoveItem()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->will($this->returnValue(new PersistenceItem()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('moveItem')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->moveItem(new Item(array('published' => false)), 0);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItem(new Item(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateQuery()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadQuery')
            ->will($this->returnValue(new PersistenceQuery(array('parameters' => array()))));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateQuery')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->updateQuery(
            new Query(
                array(
                    'published' => false,
                    'queryType' => new QueryType('type'),
                )
            ),
            new QueryUpdateStruct()
        );
    }
}
