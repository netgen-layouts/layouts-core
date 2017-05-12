<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
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
        $this->createDatabase();

        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabase();
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
     * @expectedExceptionMessage Could not find collection with identifier "999999"
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionHandler->loadCollection(999999, Value::STATUS_PUBLISHED);
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
     * @expectedExceptionMessage Could not find item with identifier "999999"
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
     * @expectedExceptionMessage Could not find query with identifier "999999"
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionHandler->loadQuery(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testLoadCollectionQuery()
    {
        $query = $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
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
            $query
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query for collection with identifier "1"
     */
    public function testLoadCollectionQueryThrowsNotFoundException()
    {
        $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = Collection::TYPE_DYNAMIC;
        $collectionCreateStruct->status = Value::STATUS_DRAFT;

        $createdCollection = $this->collectionHandler->createCollection($collectionCreateStruct);

        $this->assertInstanceOf(Collection::class, $createdCollection);

        $this->assertEquals(7, $createdCollection->id);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $createdCollection->type);
        $this->assertEquals(Value::STATUS_DRAFT, $createdCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     */
    public function testCreateCollectionWithDefaultValues()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = Collection::TYPE_DYNAMIC;
        $collectionCreateStruct->status = Value::STATUS_DRAFT;

        $createdCollection = $this->collectionHandler->createCollection($collectionCreateStruct);

        $this->assertInstanceOf(Collection::class, $createdCollection);

        $this->assertEquals(7, $createdCollection->id);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $createdCollection->type);
        $this->assertEquals(Value::STATUS_DRAFT, $createdCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollection()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->type = Collection::TYPE_MANUAL;

        $updatedCollection = $this->collectionHandler->updateCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $collectionUpdateStruct
        );

        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertEquals(3, $updatedCollection->id);
        $this->assertEquals(Collection::TYPE_MANUAL, $updatedCollection->type);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollectionWithDefaultValues()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        $updatedCollection = $this->collectionHandler->updateCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $collectionUpdateStruct
        );

        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertEquals(3, $updatedCollection->id);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $updatedCollection->type);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     */
    public function testCopyCollection()
    {
        $copiedCollection = $this->collectionHandler->copyCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(7, $copiedCollection->id);
        $this->assertInstanceOf(Collection::class, $copiedCollection);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $copiedCollection->type);
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
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => $copiedCollection->id,
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
            $this->collectionHandler->loadCollectionQuery($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
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
            new Query(
                array(
                    'id' => 2,
                    'collectionId' => 3,
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
            $this->collectionHandler->loadCollectionQuery($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "3"
     */
    public function testDeleteCollection()
    {
        $this->collectionHandler->deleteCollection(3);

        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "3"
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
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
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
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testAddItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 9999;
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
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
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
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     */
    public function testAddQuery()
    {
        $collection = $this->collectionHandler->createCollection(
            new CollectionCreateStruct(
                array(
                    'type' => Collection::TYPE_DYNAMIC,
                    'status' => Value::STATUS_PUBLISHED,
                )
            )
        );

        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => $collection->id,
                    'type' => 'ezcontent_search',
                    'parameters' => array('param' => 'value'),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addQuery(
                $collection,
                $queryCreateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Provided collection cannot have a query because it is a manual collection.
     */
    public function testAddQueryThrowsBadStateExceptionWithManualCollection()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->collectionHandler->addQuery(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $queryCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Provided collection already has a query.
     */
    public function testAddQueryThrowsBadStateExceptionWithExistingQuery()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->type = 'ezcontent_search';
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
        $queryUpdateStruct->type = 'new_type';
        $queryUpdateStruct->parameters = array(
            'parent_location_id' => 3,
            'some_param' => 'Some value',
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'type' => 'new_type',
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQuery
     */
    public function testUpdateQueryWithDefaultValues()
    {
        $queryUpdateStruct = new QueryUpdateStruct();

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
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
            $this->collectionHandler->updateQuery(
                $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
                $queryUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @doesNotPerformAssertions
     */
    public function testDeleteQuery()
    {
        $this->collectionHandler->deleteQuery(
            $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED)
        );

        try {
            $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED);
            self::fail('Query still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
