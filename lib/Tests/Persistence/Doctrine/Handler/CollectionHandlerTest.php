<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Values\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\QueryUpdateStruct;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class CollectionHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getCollectionSelectQuery
     */
    public function testLoadCollection()
    {
        $this->assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'type' => Collection::TYPE_MANUAL,
                    'shared' => false,
                    'name' => null,
                    'status' => Value::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionHandler->loadCollection(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadSharedCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadSharedCollectionsData
     */
    public function testLoadSharedCollections()
    {
        $collections = $this->collectionHandler->loadSharedCollections(Value::STATUS_PUBLISHED);

        $this->assertNotEmpty($collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(Collection::class, $collection);
            $this->assertTrue($collection->shared);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadSharedCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadSharedCollectionsData
     */
    public function testLoadSharedCollectionsInNonExistentStatus()
    {
        $collections = $this->collectionHandler->loadSharedCollections(Value::STATUS_ARCHIVED);

        $this->assertEmpty($collections);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     */
    public function testLoadItem()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->collectionHandler->loadItem(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItems()
    {
        $items = $this->collectionHandler->loadCollectionItems(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );

        $this->assertNotEmpty($items);

        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getQuerySelectQuery
     */
    public function testLoadQuery()
    {
        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'position' => 0,
                    'identifier' => 'default',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'offset' => 0,
                        'query_type' => 'list',
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionHandler->loadQuery(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueries
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueriesData
     */
    public function testLoadCollectionQueries()
    {
        $queries = $this->collectionHandler->loadCollectionQueries(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED)
        );

        $this->assertNotEmpty($queries);

        foreach ($queries as $query) {
            $this->assertInstanceOf(Query::class, $query);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionExists()
    {
        $this->assertTrue($this->collectionHandler->collectionExists(1, Value::STATUS_DRAFT));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExists()
    {
        $this->assertFalse($this->collectionHandler->collectionExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExistsInStatus()
    {
        $this->assertFalse($this->collectionHandler->collectionExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isSharedCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testIsSharedCollection()
    {
        $this->assertTrue($this->collectionHandler->isSharedCollection(3));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isSharedCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testIsSharedCollectionReturnsFalse()
    {
        $this->assertFalse($this->collectionHandler->isSharedCollection(2));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionNameExists
     */
    public function testCollectionNameExists()
    {
        $this->assertTrue($this->collectionHandler->collectionNameExists('My collection', null, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionNameExists
     */
    public function testCollectionNameNotExists()
    {
        $this->assertFalse($this->collectionHandler->collectionNameExists('Non existent', null, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionNameExists
     */
    public function testCollectionNameNotExistsWithExcludedId()
    {
        $this->assertFalse($this->collectionHandler->collectionNameExists('My collection', 3, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionNameExists
     */
    public function testCollectionNameNotExistsInStatus()
    {
        $this->assertFalse($this->collectionHandler->collectionNameExists('My collection', null, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = Collection::TYPE_DYNAMIC;
        $collectionCreateStruct->shared = false;
        $collectionCreateStruct->name = 'New collection';
        $collectionCreateStruct->status = Value::STATUS_DRAFT;

        $createdCollection = $this->collectionHandler->createCollection($collectionCreateStruct);

        $this->assertInstanceOf(Collection::class, $createdCollection);

        $this->assertEquals(6, $createdCollection->id);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $createdCollection->type);
        $this->assertFalse($createdCollection->shared);
        $this->assertEquals('New collection', $createdCollection->name);
        $this->assertEquals(Value::STATUS_DRAFT, $createdCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollection()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->type = Collection::TYPE_DYNAMIC;
        $collectionUpdateStruct->name = 'Updated collection';

        $updatedCollection = $this->collectionHandler->updateCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $collectionUpdateStruct
        );

        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertEquals(3, $updatedCollection->id);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $updatedCollection->type);
        $this->assertTrue($updatedCollection->shared);
        $this->assertEquals('Updated collection', $updatedCollection->name);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueriesData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     */
    public function testCopyCollection()
    {
        $copiedCollection = $this->collectionHandler->copyCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            'New name'
        );

        $this->assertEquals(6, $copiedCollection->id);
        $this->assertInstanceOf(Collection::class, $copiedCollection);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $copiedCollection->type);
        $this->assertTrue($copiedCollection->shared);
        $this->assertEquals('New name', $copiedCollection->name);
        $this->assertEquals(Value::STATUS_PUBLISHED, $copiedCollection->status);

        $this->assertEquals(
            array(
                new Item(
                    array(
                        'id' => 13,
                        'collectionId' => $copiedCollection->id,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '72',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
                new Item(
                    array(
                        'id' => 14,
                        'collectionId' => $copiedCollection->id,
                        'position' => 3,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '73',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
                new Item(
                    array(
                        'id' => 15,
                        'collectionId' => $copiedCollection->id,
                        'position' => 5,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '74',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems($copiedCollection)
        );

        $this->assertEquals(
            array(
                new Query(
                    array(
                        'id' => 5,
                        'collectionId' => $copiedCollection->id,
                        'position' => 0,
                        'identifier' => 'default',
                        'type' => 'ezcontent_search',
                        'parameters' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'offset' => 0,
                            'query_type' => 'list',
                        ),
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
                new Query(
                    array(
                        'id' => 6,
                        'collectionId' => $copiedCollection->id,
                        'position' => 1,
                        'identifier' => 'featured',
                        'type' => 'ezcontent_search',
                        'parameters' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'offset' => 0,
                            'query_type' => 'list',
                        ),
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionQueries($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueriesData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     */
    public function testCreateCollectionStatus()
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            Value::STATUS_ARCHIVED
        );

        $this->assertInstanceOf(Collection::class, $copiedCollection);

        $this->assertEquals(3, $copiedCollection->id);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $copiedCollection->type);
        $this->assertTrue($copiedCollection->shared);
        $this->assertEquals('My collection', $copiedCollection->name);
        $this->assertEquals(Value::STATUS_ARCHIVED, $copiedCollection->status);

        $this->assertEquals(
            array(
                new Item(
                    array(
                        'id' => 7,
                        'collectionId' => 3,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '72',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 8,
                        'collectionId' => 3,
                        'position' => 3,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '73',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 9,
                        'collectionId' => 3,
                        'position' => 5,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '74',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems($copiedCollection)
        );

        $this->assertEquals(
            array(
                new Query(
                    array(
                        'id' => 2,
                        'collectionId' => 3,
                        'position' => 0,
                        'identifier' => 'default',
                        'type' => 'ezcontent_search',
                        'parameters' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'offset' => 0,
                            'query_type' => 'list',
                        ),
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
                new Query(
                    array(
                        'id' => 3,
                        'collectionId' => 3,
                        'position' => 1,
                        'identifier' => 'featured',
                        'type' => 'ezcontent_search',
                        'parameters' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'offset' => 0,
                            'query_type' => 'list',
                        ),
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionQueries($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionQueries
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteCollection()
    {
        $this->collectionHandler->deleteCollection(3);

        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionQueries
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteCollectionInOneStatus()
    {
        $this->collectionHandler->deleteCollection(3, Value::STATUS_DRAFT);

        // First, verify that NOT all collection statuses are deleted
        try {
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the collection in draft status deleted other/all statuses.');
        }

        $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItem()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 1;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Value::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $itemCreateStruct
            )
        );

        $secondItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertEquals(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemWithNoPosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 1,
                    'position' => 3,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Value::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $itemCreateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionOnNegativePosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = -1;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = -9999;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testMoveItem()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
                1
            )
        );

        $firstItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertEquals(0, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testMoveItemToLowerPosition()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 2,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '73',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT),
                0
            )
        );

        $firstItem = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);
        $this->assertEquals(1, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionOnNegativePosition()
    {
        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testDeleteItem()
    {
        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT)
        );

        $secondItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        $this->assertEquals(1, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::queryExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::queryExists
     */
    public function testQueryExists()
    {
        $this->assertTrue(
            $this->collectionHandler->queryExists(
                $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
                'default'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::queryExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::queryExists
     */
    public function testQueryNotExists()
    {
        $this->assertFalse(
            $this->collectionHandler->queryExists(
                $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
                'featured'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     */
    public function testAddQuery()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->position = 1;
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => 3,
                    'position' => 1,
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                    'parameters' => array('param' => 'value'),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addQuery(
                $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
                $queryCreateStruct
            )
        );

        $secondQuery = $this->collectionHandler->loadQuery(3, Value::STATUS_PUBLISHED);
        $this->assertEquals(2, $secondQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     */
    public function testAddQueryWithNoPosition()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => 3,
                    'position' => 2,
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                    'parameters' => array('param' => 'value'),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addQuery(
                $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
                $queryCreateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionOnNegativePosition()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->position = -1;
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->collectionHandler->addQuery(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $queryCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionOnTooLargePosition()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'new_query';
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->position = -9999;
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->collectionHandler->addQuery(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $queryCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQuery
     */
    public function testUpdateQuery()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->identifier = 'new_identifier';
        $queryUpdateStruct->parameters = array(
            'parent_location_id' => 3,
            'some_param' => 'Some value',
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'position' => 0,
                    'identifier' => 'new_identifier',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'parent_location_id' => 3,
                        'some_param' => 'Some value',
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->updateQuery(
                $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
                $queryUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     */
    public function testMoveQuery()
    {
        $this->assertEquals(
            new Query(
                array(
                    'id' => 2,
                    'collectionId' => 3,
                    'position' => 1,
                    'identifier' => 'default',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'offset' => 0,
                        'query_type' => 'list',
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveQuery(
                $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED),
                1
            )
        );

        $firstQuery = $this->collectionHandler->loadQuery(3, Value::STATUS_PUBLISHED);
        $this->assertEquals(0, $firstQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     */
    public function testMoveQueryToLowerPosition()
    {
        $this->assertEquals(
            new Query(
                array(
                    'id' => 3,
                    'collectionId' => 3,
                    'position' => 0,
                    'identifier' => 'featured',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'offset' => 0,
                        'query_type' => 'list',
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveQuery(
                $this->collectionHandler->loadQuery(3, Value::STATUS_PUBLISHED),
                0
            )
        );

        $firstQuery = $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED);
        $this->assertEquals(1, $firstQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionOnNegativePosition()
    {
        $this->collectionHandler->moveQuery(
            $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED),
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->collectionHandler->moveQuery(
            $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     */
    public function testDeleteQuery()
    {
        $this->collectionHandler->deleteQuery(
            $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED)
        );

        $secondQuery = $this->collectionHandler->loadQuery(3, Value::STATUS_PUBLISHED);
        $this->assertEquals(0, $secondQuery->position);

        try {
            $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED);
            self::fail('Query still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
