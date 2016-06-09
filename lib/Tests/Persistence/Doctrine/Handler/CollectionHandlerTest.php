<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Core\Values\QueryCreateStruct;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Exception\NotFoundException;

class CollectionHandlerTest extends \PHPUnit\Framework\TestCase
{
    use TestCase;

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
        self::assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'type' => Collection::TYPE_MANUAL,
                    'name' => null,
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->loadCollection(1, Collection::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionHandler->loadCollection(999999, Collection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadNamedCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadNamedCollectionsData
     */
    public function testLoadNamedCollections()
    {
        $collections = $this->collectionHandler->loadNamedCollections(Collection::STATUS_PUBLISHED);

        self::assertNotEmpty($collections);

        foreach ($collections as $collection) {
            self::assertInstanceOf(Collection::class, $collection);
            self::assertEquals(Collection::TYPE_NAMED, $collection->type);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadNamedCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadNamedCollectionsData
     */
    public function testLoadNamedCollectionsInNonExistentStatus()
    {
        $collections = $this->collectionHandler->loadNamedCollections(Collection::STATUS_ARCHIVED);

        self::assertEmpty($collections);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     */
    public function testLoadItem()
    {
        self::assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '70',
                    'valueType' => 'ezcontent',
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->loadItem(1, Collection::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->collectionHandler->loadItem(999999, Collection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItems()
    {
        $items = $this->collectionHandler->loadCollectionItems(1, Collection::STATUS_DRAFT);

        self::assertNotEmpty($items);

        foreach ($items as $item) {
            self::assertInstanceOf(Item::class, $item);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItemsForNonExistentCollection()
    {
        $items = $this->collectionHandler->loadCollectionItems(999999, Collection::STATUS_PUBLISHED);

        self::assertEmpty($items);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getQuerySelectQuery
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
                    'parameters' => array(
                        'parent_location_id' => 2,
                    ),
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->loadQuery(1, Collection::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionHandler->loadQuery(999999, Collection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueries
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueriesData
     */
    public function testLoadCollectionQueries()
    {
        $queries = $this->collectionHandler->loadCollectionQueries(2, Collection::STATUS_PUBLISHED);

        self::assertNotEmpty($queries);

        foreach ($queries as $query) {
            self::assertInstanceOf(Query::class, $query);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQueries
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueriesData
     */
    public function testLoadCollectionQueriesForNonExistentCollection()
    {
        $queries = $this->collectionHandler->loadCollectionQueries(999999, Collection::STATUS_PUBLISHED);

        self::assertEmpty($queries);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionExists()
    {
        self::assertTrue($this->collectionHandler->collectionExists(1, Collection::STATUS_DRAFT));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExists()
    {
        self::assertFalse($this->collectionHandler->collectionExists(999999, Collection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExistsInStatus()
    {
        self::assertFalse($this->collectionHandler->collectionExists(1, Collection::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isNamedCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testIsNamedCollection()
    {
        self::assertTrue($this->collectionHandler->isNamedCollection(3, Collection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isNamedCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testIsNamedCollectionReturnsFalse()
    {
        self::assertFalse($this->collectionHandler->isNamedCollection(2, Collection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::namedCollectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::namedCollectionExists
     */
    public function testNamedCollectionExists()
    {
        self::assertTrue($this->collectionHandler->namedCollectionExists('My collection', Collection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::namedCollectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::namedCollectionExists
     */
    public function testNamedCollectionNotExists()
    {
        self::assertFalse($this->collectionHandler->namedCollectionExists('Non existent', Collection::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::namedCollectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::namedCollectionExists
     */
    public function testNamedCollectionNotExistsInStatus()
    {
        self::assertFalse($this->collectionHandler->namedCollectionExists('My collection', Collection::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = Collection::TYPE_NAMED;
        $collectionCreateStruct->name = 'New collection';

        $createdCollection = $this->collectionHandler->createCollection(
            $collectionCreateStruct,
            Collection::STATUS_DRAFT
        );

        self::assertInstanceOf(Collection::class, $createdCollection);

        self::assertEquals(5, $createdCollection->id);
        self::assertEquals(Collection::TYPE_NAMED, $createdCollection->type);
        self::assertEquals('New collection', $createdCollection->name);
        self::assertEquals(Collection::STATUS_DRAFT, $createdCollection->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollection()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Updated collection';

        $updatedCollection = $this->collectionHandler->updateCollection(
            3,
            Collection::STATUS_PUBLISHED,
            $collectionUpdateStruct
        );

        self::assertInstanceOf(Collection::class, $updatedCollection);

        self::assertEquals(3, $updatedCollection->id);
        self::assertEquals(Collection::TYPE_NAMED, $updatedCollection->type);
        self::assertEquals('Updated collection', $updatedCollection->name);
        self::assertEquals(Collection::STATUS_PUBLISHED, $updatedCollection->status);
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
        $copiedCollectionId = $this->collectionHandler->copyCollection(3);

        self::assertEquals(5, $copiedCollectionId);

        $copiedCollection = $this->collectionHandler->loadCollection($copiedCollectionId, Collection::STATUS_PUBLISHED);

        self::assertInstanceOf(Collection::class, $copiedCollection);
        self::assertEquals(Collection::TYPE_NAMED, $copiedCollection->type);
        self::assertRegExp('/^My collection \(copy\) \d+$/', $copiedCollection->name);
        self::assertEquals(Collection::STATUS_PUBLISHED, $copiedCollection->status);

        self::assertEquals(
            array(
                new Item(
                    array(
                        'id' => 13,
                        'collectionId' => $copiedCollectionId,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '70',
                        'valueType' => 'ezcontent',
                        'status' => Collection::STATUS_PUBLISHED,
                    )
                ),
                new Item(
                    array(
                        'id' => 14,
                        'collectionId' => $copiedCollectionId,
                        'position' => 3,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '71',
                        'valueType' => 'ezcontent',
                        'status' => Collection::STATUS_PUBLISHED,
                    )
                ),
                new Item(
                    array(
                        'id' => 15,
                        'collectionId' => $copiedCollectionId,
                        'position' => 5,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '72',
                        'valueType' => 'ezcontent',
                        'status' => Collection::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems($copiedCollectionId, Collection::STATUS_PUBLISHED)
        );

        self::assertEquals(
            array(
                new Query(
                    array(
                        'id' => 5,
                        'collectionId' => $copiedCollectionId,
                        'position' => 0,
                        'identifier' => 'default',
                        'type' => 'ezcontent_search',
                        'parameters' => array(
                            'parent_location_id' => 2,
                        ),
                        'status' => Collection::STATUS_PUBLISHED,
                    )
                ),
                new Query(
                    array(
                        'id' => 6,
                        'collectionId' => $copiedCollectionId,
                        'position' => 1,
                        'identifier' => 'featured',
                        'type' => 'ezcontent_search',
                        'parameters' => array(
                            'parent_location_id' => 2,
                        ),
                        'status' => Collection::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionQueries($copiedCollectionId, Collection::STATUS_PUBLISHED)
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
        $copiedCollection = $this->collectionHandler->createCollectionStatus(3, Collection::STATUS_PUBLISHED, Collection::STATUS_ARCHIVED);

        self::assertInstanceOf(Collection::class, $copiedCollection);

        self::assertEquals(3, $copiedCollection->id);
        self::assertEquals(Collection::TYPE_NAMED, $copiedCollection->type);
        self::assertEquals('My collection', $copiedCollection->name);
        self::assertEquals(Collection::STATUS_ARCHIVED, $copiedCollection->status);

        self::assertEquals(
            array(
                new Item(
                    array(
                        'id' => 7,
                        'collectionId' => 3,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '70',
                        'valueType' => 'ezcontent',
                        'status' => Collection::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 8,
                        'collectionId' => 3,
                        'position' => 3,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '71',
                        'valueType' => 'ezcontent',
                        'status' => Collection::STATUS_ARCHIVED,
                    )
                ),
                new Item(
                    array(
                        'id' => 9,
                        'collectionId' => 3,
                        'position' => 5,
                        'type' => Item::TYPE_MANUAL,
                        'valueId' => '72',
                        'valueType' => 'ezcontent',
                        'status' => Collection::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems(3, Collection::STATUS_ARCHIVED)
        );

        self::assertEquals(
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
                        ),
                        'status' => Collection::STATUS_ARCHIVED,
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
                        ),
                        'status' => Collection::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionQueries(3, Collection::STATUS_ARCHIVED)
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

        $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);
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
        $this->collectionHandler->deleteCollection(3, Collection::STATUS_DRAFT);

        // First, verify that NOT all collection statuses are deleted
        try {
            $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the collection in draft status deleted other/all statuses.');
        }

        $this->collectionHandler->loadCollection(3, Collection::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItem()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        self::assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->addItem(1, Collection::STATUS_DRAFT, $itemCreateStruct, 1)
        );

        $secondItem = $this->collectionHandler->loadItem(2, Collection::STATUS_DRAFT);
        self::assertEquals(2, $secondItem->position);
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

        self::assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 1,
                    'position' => 3,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->addItem(1, Collection::STATUS_DRAFT, $itemCreateStruct)
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
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->collectionHandler->addItem(1, Collection::STATUS_DRAFT, $itemCreateStruct, -1);
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
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'ezcontent';

        $this->collectionHandler->addItem(1, Collection::STATUS_DRAFT, $itemCreateStruct, -9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testMoveItem()
    {
        self::assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '70',
                    'valueType' => 'ezcontent',
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->moveItem(1, Collection::STATUS_DRAFT, 1)
        );

        $firstItem = $this->collectionHandler->loadItem(2, Collection::STATUS_DRAFT);
        self::assertEquals(0, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testMoveItemToLowerPosition()
    {
        self::assertEquals(
            new Item(
                array(
                    'id' => 2,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '71',
                    'valueType' => 'ezcontent',
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->moveItem(2, Collection::STATUS_DRAFT, 0)
        );

        $firstItem = $this->collectionHandler->loadItem(1, Collection::STATUS_DRAFT);
        self::assertEquals(1, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionOnNegativePosition()
    {
        $this->collectionHandler->moveItem(1, Collection::STATUS_DRAFT, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->collectionHandler->moveItem(1, Collection::STATUS_DRAFT, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     */
    public function testDeleteItem()
    {
        $this->collectionHandler->deleteItem(2, Collection::STATUS_DRAFT);

        $secondItem = $this->collectionHandler->loadItem(3, Collection::STATUS_DRAFT);
        self::assertEquals(1, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(2, Collection::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::queryIdentifierExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::queryIdentifierExists
     */
    public function testQueryIdentifierExists()
    {
        self::assertTrue($this->collectionHandler->queryIdentifierExists(2, Collection::STATUS_PUBLISHED, 'default'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::queryIdentifierExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::queryIdentifierExists
     */
    public function testQueryIdentifierNotExists()
    {
        self::assertFalse($this->collectionHandler->queryIdentifierExists(2, Collection::STATUS_PUBLISHED, 'featured'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::queryIdentifierExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::queryIdentifierExists
     */
    public function testQueryIdentifierNotExistsInStatus()
    {
        self::assertFalse($this->collectionHandler->queryIdentifierExists(2, Collection::STATUS_ARCHIVED, 'default'));
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
        $queryCreateStruct->setParameter('param', 'value');

        self::assertEquals(
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => 3,
                    'position' => 1,
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                    'parameters' => array('param' => 'value'),
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addQuery(3, Collection::STATUS_PUBLISHED, $queryCreateStruct, 1)
        );

        $secondQuery = $this->collectionHandler->loadQuery(3, Collection::STATUS_PUBLISHED);
        self::assertEquals(2, $secondQuery->position);
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
        $queryCreateStruct->setParameter('param', 'value');

        self::assertEquals(
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => 3,
                    'position' => 2,
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                    'parameters' => array('param' => 'value'),
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->addQuery(3, Collection::STATUS_PUBLISHED, $queryCreateStruct)
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
        $queryCreateStruct->setParameter('param', 'value');

        $this->collectionHandler->addQuery(3, Collection::STATUS_PUBLISHED, $queryCreateStruct, -1);
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
        $queryCreateStruct->setParameter('param', 'value');

        $this->collectionHandler->addQuery(3, Collection::STATUS_PUBLISHED, $queryCreateStruct, -9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQuery
     */
    public function testUpdateQuery()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->identifier = 'new_identifier';
        $queryUpdateStruct->setParameter('parent_location_id', 3);
        $queryUpdateStruct->setParameter('some_param', 'Some value');

        self::assertEquals(
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
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->updateQuery(1, Collection::STATUS_PUBLISHED, $queryUpdateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
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
                    'parameters' => array(
                        'parent_location_id' => 2,
                    ),
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveQuery(2, Collection::STATUS_PUBLISHED, 1)
        );

        $firstQuery = $this->collectionHandler->loadQuery(3, Collection::STATUS_PUBLISHED);
        self::assertEquals(0, $firstQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
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
                    'parameters' => array(
                        'parent_location_id' => 2,
                    ),
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->moveQuery(3, Collection::STATUS_PUBLISHED, 0)
        );

        $firstQuery = $this->collectionHandler->loadQuery(2, Collection::STATUS_PUBLISHED);
        self::assertEquals(1, $firstQuery->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionOnNegativePosition()
    {
        $this->collectionHandler->moveQuery(2, Collection::STATUS_PUBLISHED, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::moveQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->collectionHandler->moveQuery(2, Collection::STATUS_PUBLISHED, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperQueryConditions
     */
    public function testDeleteQuery()
    {
        $this->collectionHandler->deleteQuery(2, Collection::STATUS_PUBLISHED);

        $secondQuery = $this->collectionHandler->loadQuery(3, Collection::STATUS_PUBLISHED);
        self::assertEquals(0, $secondQuery->position);

        try {
            $this->collectionHandler->loadQuery(2, Collection::STATUS_PUBLISHED);
            self::fail('Query still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
