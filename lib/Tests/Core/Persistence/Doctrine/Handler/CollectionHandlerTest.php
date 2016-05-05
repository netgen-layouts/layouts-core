<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Core\Values\QueryCreateStruct;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\API\Exception\NotFoundException;

class CollectionHandlerTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();

        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::__construct
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionSelectQuery
     */
    public function testLoadCollection()
    {
        self::assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'type' => APICollection::TYPE_MANUAL,
                    'name' => null,
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->loadCollection(1, APICollection::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionSelectQuery
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionHandler->loadCollection(999999, APICollection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadNamedCollections
     */
    public function testLoadNamedCollections()
    {
        $collections = $this->collectionHandler->loadNamedCollections(APICollection::STATUS_PUBLISHED);

        self::assertNotEmpty($collections);

        foreach ($collections as $collection) {
            self::assertInstanceOf(Collection::class, $collection);
            self::assertEquals(APICollection::TYPE_NAMED, $collection->type);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadNamedCollections
     */
    public function testLoadNamedCollectionsInNonExistentStatus()
    {
        $collections = $this->collectionHandler->loadNamedCollections(APICollection::STATUS_ARCHIVED);

        self::assertEmpty($collections);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemSelectQuery
     */
    public function testLoadItem()
    {
        self::assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => APIItem::TYPE_MANUAL,
                    'valueId' => '70',
                    'valueType' => 'ezcontent',
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->loadItem(1, APICollection::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemSelectQuery
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->collectionHandler->loadItem(999999, APICollection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItems()
    {
        $items = $this->collectionHandler->loadCollectionItems(1, APICollection::STATUS_PUBLISHED);

        self::assertNotEmpty($items);

        foreach ($items as $item) {
            self::assertInstanceOf(Item::class, $item);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItemsForNonExistentCollection()
    {
        $items = $this->collectionHandler->loadCollectionItems(999999, APICollection::STATUS_PUBLISHED);

        self::assertEmpty($items);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQuerySelectQuery
     */
    public function testLoadQuery()
    {
        self::assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'position' => 0,
                    'identifier' => 'default',
                    'type' => 'ezcontent_search',
                    'parameters' => array(),
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->loadQuery(1, APICollection::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQuerySelectQuery
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionHandler->loadQuery(999999, APICollection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueries
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueriesData
     */
    public function testLoadCollectionQueries()
    {
        $queries = $this->collectionHandler->loadCollectionQueries(2, APICollection::STATUS_PUBLISHED);

        self::assertNotEmpty($queries);

        foreach ($queries as $query) {
            self::assertInstanceOf(Query::class, $query);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueries
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueriesData
     */
    public function testLoadCollectionQueriesForNonExistentCollection()
    {
        $queries = $this->collectionHandler->loadCollectionQueries(999999, APICollection::STATUS_PUBLISHED);

        self::assertEmpty($queries);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     */
    public function testCollectionExists()
    {
        self::assertTrue($this->collectionHandler->collectionExists(1, APICollection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     */
    public function testCollectionNotExists()
    {
        self::assertFalse($this->collectionHandler->collectionExists(999999, APICollection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     */
    public function testCollectionNotExistsInStatus()
    {
        self::assertFalse($this->collectionHandler->collectionExists(1, APICollection::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::namedCollectionExists
     */
    public function testNamedCollectionExists()
    {
        self::assertTrue($this->collectionHandler->namedCollectionExists('My collection', APICollection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::namedCollectionExists
     */
    public function testNamedCollectionNotExists()
    {
        self::assertFalse($this->collectionHandler->namedCollectionExists('Non existent', APICollection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::namedCollectionExists
     */
    public function testNamedCollectionNotExistsInStatus()
    {
        self::assertFalse($this->collectionHandler->namedCollectionExists('My collection', APICollection::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionInsertQuery
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = APICollection::TYPE_MANUAL;
        $collectionCreateStruct->name = 'New collection';
        $collectionCreateStruct->status = APICollection::STATUS_PUBLISHED;

        $createdCollection = $this->collectionHandler->createCollection($collectionCreateStruct);

        self::assertInstanceOf(Collection::class, $createdCollection);

        self::assertEquals(4, $createdCollection->id);
        self::assertEquals(APICollection::TYPE_MANUAL, $createdCollection->type);
        self::assertNull($createdCollection->name);
        self::assertEquals(APICollection::STATUS_PUBLISHED, $createdCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionInsertQuery
     */
    public function testCreateNamedCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = APICollection::TYPE_NAMED;
        $collectionCreateStruct->name = 'New collection';
        $collectionCreateStruct->status = APICollection::STATUS_PUBLISHED;

        $createdCollection = $this->collectionHandler->createCollection($collectionCreateStruct);

        self::assertInstanceOf(Collection::class, $createdCollection);

        self::assertEquals(4, $createdCollection->id);
        self::assertEquals(APICollection::TYPE_NAMED, $createdCollection->type);
        self::assertEquals('New collection', $createdCollection->name);
        self::assertEquals(APICollection::STATUS_PUBLISHED, $createdCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     */
    public function testUpdateCollection()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Updated collection';

        $updatedCollection = $this->collectionHandler->updateCollection(
            1,
            APICollection::STATUS_PUBLISHED,
            $collectionUpdateStruct
        );

        self::assertInstanceOf(Collection::class, $updatedCollection);

        self::assertEquals(1, $updatedCollection->id);
        self::assertEquals(APICollection::TYPE_MANUAL, $updatedCollection->type);
        self::assertNull($updatedCollection->name);
        self::assertEquals(APICollection::STATUS_PUBLISHED, $updatedCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     */
    public function testUpdateNamedCollection()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Updated collection';

        $updatedCollection = $this->collectionHandler->updateCollection(
            3,
            APICollection::STATUS_PUBLISHED,
            $collectionUpdateStruct
        );

        self::assertInstanceOf(Collection::class, $updatedCollection);

        self::assertEquals(3, $updatedCollection->id);
        self::assertEquals(APICollection::TYPE_NAMED, $updatedCollection->type);
        self::assertEquals('Updated collection', $updatedCollection->name);
        self::assertEquals(APICollection::STATUS_PUBLISHED, $updatedCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueriesData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     */
    public function testCopyCollection()
    {
        $copiedCollection = $this->collectionHandler->copyCollection(2);

        self::assertInstanceOf(Collection::class, $copiedCollection);

        self::assertEquals(4, $copiedCollection->id);
        self::assertEquals(APICollection::TYPE_DYNAMIC, $copiedCollection->type);
        self::assertNull($copiedCollection->name);
        self::assertEquals(APICollection::STATUS_PUBLISHED, $copiedCollection->status);

        self::assertEquals(
            array(
                new Item(
                    array(
                        'id' => 12,
                        'collectionId' => 4,
                        'position' => 1,
                        'type' => APIItem::TYPE_MANUAL,
                        'valueId' => '70',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_PUBLISHED,
                    )
                ),
                new Item(
                    array(
                        'id' => 13,
                        'collectionId' => 4,
                        'position' => 2,
                        'type' => APIItem::TYPE_MANUAL,
                        'valueId' => '71',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_PUBLISHED,
                    )
                ),
                new Item(
                    array(
                        'id' => 14,
                        'collectionId' => 4,
                        'position' => 5,
                        'type' => APIItem::TYPE_OVERRIDE,
                        'valueId' => '72',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems(4, APICollection::STATUS_PUBLISHED)
        );

        self::assertEquals(
            array(
                new Query(
                    array(
                        'id' => 4,
                        'collectionId' => 4,
                        'position' => 0,
                        'identifier' => 'default',
                        'type' => 'ezcontent_search',
                        'parameters' => array(),
                        'status' => APICollection::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionQueries(4, APICollection::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueriesData
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     */
    public function testCopyNamedCollection()
    {
        $copiedCollection = $this->collectionHandler->copyCollection(3);

        self::assertInstanceOf(Collection::class, $copiedCollection);

        self::assertEquals(4, $copiedCollection->id);
        self::assertEquals(APICollection::TYPE_NAMED, $copiedCollection->type);
        self::assertRegExp('/^My collection \(copy\) \d+$/', $copiedCollection->name);
        self::assertEquals(APICollection::STATUS_PUBLISHED, $copiedCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     */
    public function testCreateCollectionStatus()
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(2, APICollection::STATUS_PUBLISHED, APICollection::STATUS_ARCHIVED);

        self::assertInstanceOf(Collection::class, $copiedCollection);

        self::assertEquals(2, $copiedCollection->id);
        self::assertEquals(APICollection::TYPE_DYNAMIC, $copiedCollection->type);
        self::assertNull($copiedCollection->name);
        self::assertEquals(APICollection::STATUS_ARCHIVED, $copiedCollection->status);

        self::assertEquals(
            array(
                new Item(
                    array(
                        'id' => 4,
                        'collectionId' => 2,
                        'position' => 1,
                        'type' => APIItem::TYPE_MANUAL,
                        'valueId' => '70',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 5,
                        'collectionId' => 2,
                        'position' => 2,
                        'type' => APIItem::TYPE_MANUAL,
                        'valueId' => '71',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 6,
                        'collectionId' => 2,
                        'position' => 5,
                        'type' => APIItem::TYPE_OVERRIDE,
                        'valueId' => '72',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems(2, APICollection::STATUS_ARCHIVED)
        );

        self::assertEquals(
            array(
                new Query(
                    array(
                        'id' => 1,
                        'collectionId' => 2,
                        'position' => 0,
                        'identifier' => 'default',
                        'type' => 'ezcontent_search',
                        'parameters' => array(),
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionQueries(2, APICollection::STATUS_ARCHIVED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::updateCollectionStatus
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createCollectionInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     */
    public function testUpdateCollectionStatus()
    {
        $copiedCollection = $this->collectionHandler->updateCollectionStatus(2, APICollection::STATUS_PUBLISHED, APICollection::STATUS_ARCHIVED);

        self::assertInstanceOf(Collection::class, $copiedCollection);

        self::assertEquals(2, $copiedCollection->id);
        self::assertEquals(APICollection::TYPE_DYNAMIC, $copiedCollection->type);
        self::assertNull($copiedCollection->name);
        self::assertEquals(APICollection::STATUS_ARCHIVED, $copiedCollection->status);

        self::assertEquals(
            array(
                new Item(
                    array(
                        'id' => 4,
                        'collectionId' => 2,
                        'position' => 1,
                        'type' => APIItem::TYPE_MANUAL,
                        'valueId' => '70',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 5,
                        'collectionId' => 2,
                        'position' => 2,
                        'type' => APIItem::TYPE_MANUAL,
                        'valueId' => '71',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 6,
                        'collectionId' => 2,
                        'position' => 5,
                        'type' => APIItem::TYPE_OVERRIDE,
                        'valueId' => '72',
                        'valueType' => 'ezcontent',
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems(2, APICollection::STATUS_ARCHIVED)
        );

        self::assertEquals(
            array(
                new Query(
                    array(
                        'id' => 1,
                        'collectionId' => 2,
                        'position' => 0,
                        'identifier' => 'default',
                        'type' => 'ezcontent_search',
                        'parameters' => array(),
                        'status' => APICollection::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionQueries(2, APICollection::STATUS_ARCHIVED)
        );

        try {
            $this->collectionHandler->loadCollection(2, APICollection::STATUS_PUBLISHED);
            self::fail('Collection in old status still exists after updating the status');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteCollection()
    {
        $this->collectionHandler->deleteCollection(1);

        $this->collectionHandler->loadCollection(1, APICollection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteCollectionInOneStatus()
    {
        $this->collectionHandler->deleteCollection(1, APICollection::STATUS_DRAFT);

        // First, verify that NOT all collection statuses are deleted
        try {
            $this->collectionHandler->loadCollection(1, APICollection::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the collection in draft status deleted other/all statuses.');
        }

        $this->collectionHandler->loadCollection(1, APICollection::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::itemExists
     */
    public function testItemExists()
    {
        self::assertTrue($this->collectionHandler->itemExists(2, APICollection::STATUS_PUBLISHED, 1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::itemExists
     */
    public function testItemNotExists()
    {
        self::assertFalse($this->collectionHandler->itemExists(2, APICollection::STATUS_PUBLISHED, 50));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::itemExists
     */
    public function testItemNotExistsInStatus()
    {
        self::assertFalse($this->collectionHandler->itemExists(2, APICollection::STATUS_ARCHIVED, 1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     */
    public function testAddItem()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = APIItem::TYPE_MANUAL;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        self::assertEquals(
            new Item(
                array(
                    'id' => 12,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => APIItem::TYPE_MANUAL,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addItem(1, APICollection::STATUS_PUBLISHED, $itemCreateStruct, 1)
        );

        $secondItem = $this->collectionHandler->loadItem(2, APICollection::STATUS_PUBLISHED);
        self::assertEquals(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     */
    public function testAddItemWithNoPosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = APIItem::TYPE_MANUAL;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        self::assertEquals(
            new Item(
                array(
                    'id' => 12,
                    'collectionId' => 1,
                    'position' => 3,
                    'type' => APIItem::TYPE_MANUAL,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addItem(1, APICollection::STATUS_PUBLISHED, $itemCreateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     */
    public function testAddItemInNonManualCollection()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = APIItem::TYPE_MANUAL;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        self::assertEquals(
            new Item(
                array(
                    'id' => 12,
                    'collectionId' => 2,
                    'position' => 50,
                    'type' => APIItem::TYPE_MANUAL,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addItem(2, APICollection::STATUS_PUBLISHED, $itemCreateStruct, 50)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionOnNegativePosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = APIItem::TYPE_MANUAL;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->collectionHandler->addItem(1, APICollection::STATUS_PUBLISHED, $itemCreateStruct, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createItemInsertQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = APIItem::TYPE_MANUAL;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->collectionHandler->addItem(1, APICollection::STATUS_PUBLISHED, $itemCreateStruct, -9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testMoveItem()
    {
        self::assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => APIItem::TYPE_MANUAL,
                    'valueId' => '70',
                    'valueType' => 'ezcontent',
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveItem(1, APICollection::STATUS_PUBLISHED, 1)
        );

        $firstItem = $this->collectionHandler->loadItem(2, APICollection::STATUS_PUBLISHED);
        self::assertEquals(0, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testMoveItemToLowerPosition()
    {
        self::assertEquals(
            new Item(
                array(
                    'id' => 2,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => APIItem::TYPE_MANUAL,
                    'valueId' => '71',
                    'valueType' => 'ezcontent',
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveItem(2, APICollection::STATUS_PUBLISHED, 0)
        );

        $firstItem = $this->collectionHandler->loadItem(1, APICollection::STATUS_PUBLISHED);
        self::assertEquals(1, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testMoveItemInNonManualCollection()
    {
        self::assertEquals(
            new Item(
                array(
                    'id' => 4,
                    'collectionId' => 2,
                    'position' => 50,
                    'type' => APIItem::TYPE_MANUAL,
                    'valueId' => '70',
                    'valueType' => 'ezcontent',
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveItem(4, APICollection::STATUS_PUBLISHED, 50)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionOnNegativePosition()
    {
        $this->collectionHandler->moveItem(1, APICollection::STATUS_PUBLISHED, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->collectionHandler->moveItem(1, APICollection::STATUS_PUBLISHED, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testDeleteItem()
    {
        $this->collectionHandler->deleteItem(2, APICollection::STATUS_PUBLISHED);

        $secondItem = $this->collectionHandler->loadItem(3, APICollection::STATUS_PUBLISHED);
        self::assertEquals(1, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(2, APICollection::STATUS_PUBLISHED);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     */
    public function testDeleteItemInNonManualCollection()
    {
        $this->collectionHandler->deleteItem(5, APICollection::STATUS_PUBLISHED);

        $secondItem = $this->collectionHandler->loadItem(6, APICollection::STATUS_PUBLISHED);
        self::assertEquals(5, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(5, APICollection::STATUS_PUBLISHED);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::queryExists
     */
    public function testQueryExists()
    {
        self::assertTrue($this->collectionHandler->queryExists(2, APICollection::STATUS_PUBLISHED, 'default'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::queryExists
     */
    public function testQueryNotExists()
    {
        self::assertFalse($this->collectionHandler->queryExists(2, APICollection::STATUS_PUBLISHED, 'featured'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::queryExists
     */
    public function testQueryNotExistsInStatus()
    {
        self::assertFalse($this->collectionHandler->queryExists(2, APICollection::STATUS_ARCHIVED, 'default'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     */
    public function testAddQuery()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->setParameter('param', 'value');

        self::assertEquals(
            new Query(
                array(
                    'id' => 4,
                    'collectionId' => 3,
                    'position' => 1,
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                    'parameters' => array('param' => 'value'),
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addQuery(3, APICollection::STATUS_PUBLISHED, $queryCreateStruct, 1)
        );

        $secondQuery = $this->collectionHandler->loadQuery(3, APICollection::STATUS_PUBLISHED);
        self::assertEquals(2, $secondQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     */
    public function testAddQueryWithNoPosition()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->setParameter('param', 'value');

        self::assertEquals(
            new Query(
                array(
                    'id' => 4,
                    'collectionId' => 3,
                    'position' => 2,
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                    'parameters' => array('param' => 'value'),
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addQuery(3, APICollection::STATUS_PUBLISHED, $queryCreateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionOnNegativePosition()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->setParameter('param', 'value');

        $this->collectionHandler->addQuery(3, APICollection::STATUS_PUBLISHED, $queryCreateStruct, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::createQueryInsertQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionOnTooLargePosition()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->setParameter('param', 'value');

        $this->collectionHandler->addQuery(3, APICollection::STATUS_PUBLISHED, $queryCreateStruct, -9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::updateQuery
     */
    public function testUpdateQuery()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->identifier = 'new_identifier';
        $queryUpdateStruct->setParameter('a_param', 'A value');
        $queryUpdateStruct->setParameter('some_param', 'Some other value');

        self::assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'position' => 0,
                    'identifier' => 'new_identifier',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'a_param' => 'A value',
                        'some_param' => 'Some other value',
                    ),
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->updateQuery(1, APICollection::STATUS_PUBLISHED, $queryUpdateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     */
    public function testMoveQuery()
    {
        self::assertEquals(
            new Query(
                array(
                    'id' => 2,
                    'collectionId' => 3,
                    'position' => 1,
                    'identifier' => 'default',
                    'type' => 'ezcontent_search',
                    'parameters' => array(),
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveQuery(2, APICollection::STATUS_PUBLISHED, 1)
        );

        $firstQuery = $this->collectionHandler->loadQuery(3, APICollection::STATUS_PUBLISHED);
        self::assertEquals(0, $firstQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     */
    public function testMoveQueryToLowerPosition()
    {
        self::assertEquals(
            new Query(
                array(
                    'id' => 3,
                    'collectionId' => 3,
                    'position' => 0,
                    'identifier' => 'featured',
                    'type' => 'ezcontent_search',
                    'parameters' => array(),
                    'status' => APICollection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveQuery(3, APICollection::STATUS_PUBLISHED, 0)
        );

        $firstQuery = $this->collectionHandler->loadQuery(2, APICollection::STATUS_PUBLISHED);
        self::assertEquals(1, $firstQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionOnNegativePosition()
    {
        $this->collectionHandler->moveQuery(2, APICollection::STATUS_PUBLISHED, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->collectionHandler->moveQuery(2, APICollection::STATUS_PUBLISHED, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::deleteQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     */
    public function testDeleteQuery()
    {
        $this->collectionHandler->deleteQuery(2, APICollection::STATUS_PUBLISHED);

        $secondQuery = $this->collectionHandler->loadQuery(3, APICollection::STATUS_PUBLISHED);
        self::assertEquals(0, $secondQuery->position);

        try {
            $this->collectionHandler->loadQuery(2, APICollection::STATUS_PUBLISHED);
            self::fail('Query still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
