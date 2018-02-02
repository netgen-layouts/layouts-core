<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\BlockManager\Tests\Collection\Stubs\ItemDefinition;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;

final class CollectionServiceTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService();
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
                    new PersistenceCollection()
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteCollectionQuery')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->changeCollectionType(
            new Collection(array('published' => false, 'type' => Collection::TYPE_DYNAMIC)),
            Collection::TYPE_MANUAL
        );
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
                    new PersistenceCollection()
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
            new ItemCreateStruct(array('definition' => new ItemDefinition('ezlocation'), 'type' => Item::TYPE_MANUAL))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateItem()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->will(
                $this->returnValue(
                    new PersistenceItem(array('config' => array()))
                )
            );

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateItem')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->updateItem(
            new Item(array('published' => false, 'definition' => new ItemDefinition('ezlocation'))),
            new ItemUpdateStruct()
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDeleteItems()
    {
        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadCollection')
            ->will($this->returnValue(new PersistenceCollection()));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('deleteItems')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItems(new Collection(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateQuery()
    {
        $persistenceQuery = new PersistenceQuery(
            array(
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'parameters' => array('en' => array()),
            )
        );

        $this->collectionHandlerMock
            ->expects($this->at(0))
            ->method('loadQuery')
            ->will($this->returnValue($persistenceQuery));

        $this->collectionHandlerMock
            ->expects($this->at(1))
            ->method('updateQueryTranslation')
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
            new QueryUpdateStruct(
                array(
                    'locale' => 'en',
                )
            )
        );
    }
}
