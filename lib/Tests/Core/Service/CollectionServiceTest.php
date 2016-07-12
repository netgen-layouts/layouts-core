<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionDraft;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemDraft;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryDraft;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;

abstract class CollectionServiceTest extends ServiceTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionValidatorMock;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->collectionValidatorMock = $this->createMock(CollectionValidator::class);

        $this->queryTypeRegistry = new QueryTypeRegistry();
        $this->queryTypeRegistry->addQueryType(
            new QueryType(
                'ezcontent_search',
                new QueryTypeHandler(),
                new Configuration('query_type', 'Query type')
            )
        );

        $this->collectionService = $this->createCollectionService(
            $this->collectionValidatorMock,
            $this->queryTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        self::assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionService->loadCollection(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollectionDraft
     */
    public function testLoadCollectionDraft()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        self::assertInstanceOf(CollectionDraft::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollectionDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadCollectionDraftThrowsNotFoundException()
    {
        $this->collectionService->loadCollectionDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadNamedCollections
     */
    public function testLoadNamedCollections()
    {
        $collections = $this->collectionService->loadNamedCollections();

        self::assertNotEmpty($collections);

        foreach ($collections as $collection) {
            self::assertInstanceOf(Collection::class, $collection);
            self::assertEquals(Collection::TYPE_NAMED, $collection->getType());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     */
    public function testLoadItem()
    {
        $item = $this->collectionService->loadItem(7);

        self::assertInstanceOf(Item::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItemDraft
     */
    public function testLoadItemDraft()
    {
        $item = $this->collectionService->loadItemDraft(7);

        self::assertInstanceOf(ItemDraft::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItemDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadItemDraftThrowsNotFoundException()
    {
        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     */
    public function testLoadQuery()
    {
        $query = $this->collectionService->loadQuery(2);

        self::assertInstanceOf(Query::class, $query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionService->loadQuery(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQueryDraft
     */
    public function testLoadQueryDraft()
    {
        $query = $this->collectionService->loadQueryDraft(2);

        self::assertInstanceOf(QueryDraft::class, $query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQueryDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadQueryDraftThrowsNotFoundException()
    {
        $this->collectionService->loadQueryDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            Collection::TYPE_MANUAL,
            'New name'
        );

        $createdCollection = $this->collectionService->createCollection($collectionCreateStruct);

        self::assertInstanceOf(CollectionDraft::class, $createdCollection);
        self::assertNull($createdCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     */
    public function testCreateNamedCollection()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            Collection::TYPE_NAMED,
            'New name'
        );

        $createdCollection = $this->collectionService->createCollection($collectionCreateStruct);

        self::assertInstanceOf(CollectionDraft::class, $createdCollection);
        self::assertEquals('New name', $createdCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateNamedCollectionThrowsBadStateException()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            Collection::TYPE_NAMED,
            'My collection'
        );

        $this->collectionService->createCollection($collectionCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Super cool collection';

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        self::assertInstanceOf(CollectionDraft::class, $updatedCollection);
        self::assertEquals('Super cool collection', $updatedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateCollectionWithExistingNameThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollectionDraft(5);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'My collection';

        $this->collectionService->updateCollection(
            $collection,
            $collectionUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateNonNamedCollectionThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'My collection';

        $this->collectionService->updateCollection(
            $collection,
            $collectionUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromManualToDynamic()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_DYNAMIC,
            $this->collectionService->newQueryCreateStruct(
                $this->queryTypeRegistry->getQueryType('ezcontent_search'),
                'default'
            )
        );

        self::assertInstanceOf(CollectionDraft::class, $updatedCollection);
        self::assertEquals(Collection::TYPE_DYNAMIC, $updatedCollection->getType());
        self::assertEquals(count($updatedCollection->getItems()), count($collection->getItems()));
        self::assertCount(1, $updatedCollection->getQueries());
        self::assertEquals('ezcontent_search', $updatedCollection->getQueries()[0]->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromDynamicToManual()
    {
        $collection = $this->collectionService->loadCollectionDraft(4);

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_MANUAL
        );

        self::assertInstanceOf(CollectionDraft::class, $updatedCollection);
        self::assertEquals(Collection::TYPE_MANUAL, $updatedCollection->getType());
        self::assertEquals(count($updatedCollection->getItems()), count($collection->getItems()));
        self::assertCount(0, $updatedCollection->getQueries());

        foreach ($updatedCollection->getItems() as $index => $item) {
            self::assertEquals($index, $item->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionOnChangingFromNamedCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_MANUAL
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionOnChangingToNamedCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(4);

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_NAMED
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionOnChangingToDynamicCollectionWithoutQueryCreateStruct()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_DYNAMIC
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     */
    public function testCopyCollection()
    {
        $collection = $this->collectionService->loadCollection(2);
        $copiedCollection = $this->collectionService->copyCollection($collection);

        self::assertInstanceOf(Collection::class, $copiedCollection);
        self::assertEquals(6, $copiedCollection->getId());
        self::assertNull($copiedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     */
    public function testCopyNamedCollection()
    {
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->copyCollection($collection);

        self::assertInstanceOf(Collection::class, $copiedCollection);
        self::assertEquals(6, $copiedCollection->getId());
        self::assertRegExp('/^My collection \(copy\) \d+$/', $copiedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     */
    public function testCreateDraft()
    {
        $collection = $this->collectionService->loadCollection(2);
        $draftCollection = $this->collectionService->createDraft($collection);

        self::assertInstanceOf(CollectionDraft::class, $draftCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->createDraft($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDiscardDraft()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $this->collectionService->discardDraft($collection);

        $this->collectionService->loadCollectionDraft($collection->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     */
    public function testPublishCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $publishedCollection = $this->collectionService->publishCollection($collection);

        self::assertInstanceOf(Collection::class, $publishedCollection);
        self::assertEquals(Collection::STATUS_PUBLISHED, $publishedCollection->getStatus());

        try {
            $this->collectionService->loadCollectionDraft($collection->getId());
            self::fail('Draft collection still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        $this->collectionService->deleteCollection($collection);

        $this->collectionService->loadCollection($collection->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     */
    public function testAddItem()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollectionDraft(1);

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );

        self::assertInstanceOf(ItemDraft::class, $createdItem);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->addItem($collection, $itemCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     */
    public function testMoveItem()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            1
        );

        /*
        self::assertInstanceOf(ItemDraft::class, $movedItem);
        self::assertEquals(1, $movedItem->getPosition());
        */

        $secondItem = $this->collectionService->loadItemDraft(2);
        self::assertEquals(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveItemThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItem()
    {
        $item = $this->collectionService->loadItemDraft(1);
        $this->collectionService->deleteItem($item);

        try {
            $this->collectionService->loadItemDraft($item->getId());
            self::fail('Item still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondItem = $this->collectionService->loadItemDraft(2);
        self::assertEquals(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     */
    public function testAddQuery()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType(
                'ezcontent_search',
                new QueryTypeHandler(),
                new Configuration('ezcontent_search', '')
            ),
            'new_query'
        );

        $collection = $this->collectionService->loadCollectionDraft(3);

        $createdQuery = $this->collectionService->addQuery(
            $collection,
            $queryCreateStruct,
            1
        );

        self::assertInstanceOf(QueryDraft::class, $createdQuery);

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        self::assertEquals(2, $secondQuery->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryInManualCollectionThrowsBadStateException()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType(
                'ezcontent_search',
                new QueryTypeHandler(),
                new Configuration('ezcontent_search', '')
            ),
            'new_query'
        );

        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryWithExistingIdentifierThrowsBadStateException()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType(
                'ezcontent_search',
                new QueryTypeHandler(),
                new Configuration('ezcontent_search', '')
            ),
            'default'
        );

        $collection = $this->collectionService->loadCollectionDraft(3);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType(
                'ezcontent_search',
                new QueryTypeHandler(),
                new Configuration('ezcontent_search', '')
            ),
            'new_query'
        );

        $collection = $this->collectionService->loadCollectionDraft(3);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     */
    public function testUpdateQuery()
    {
        $query = $this->collectionService->loadQueryDraft(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = 'new_identifier';
        $queryUpdateStruct->setParameter('parent_location_id', 3);
        $queryUpdateStruct->setParameter('param', 'value');

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        self::assertInstanceOf(QueryDraft::class, $updatedQuery);

        self::assertEquals('new_identifier', $updatedQuery->getIdentifier());
        self::assertEquals(
            array(
                'parent_location_id' => 3,
                'sort_direction' => 'descending',
                'sort_type' => 'date_published',
                'offset' => 0,
                'query_type' => 'list',
                'param' => 'value',
            ),
            $updatedQuery->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateQueryWithExistingIdentifierThrowsBadStateException()
    {
        $query = $this->collectionService->loadQueryDraft(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = 'featured';

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     */
    public function testMoveQuery()
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQueryDraft(2),
            1
        );

        /*
        self::assertInstanceOf(QueryDraft::class, $movedQuery);
        self::assertEquals(1, $movedQuery->getPosition());
        */

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        self::assertEquals(0, $secondQuery->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQueryDraft(2),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionOnNonNamedCollection()
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQueryDraft(4),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteQuery
     */
    public function testDeleteQuery()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $query = $this->collectionService->loadQueryDraft(2);
        $this->collectionService->deleteQuery($query);

        $collectionAfterDelete = $this->collectionService->loadCollectionDraft(3);

        try {
            $this->collectionService->loadQueryDraft($query->getId());
            self::fail('Query still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        self::assertEquals(0, $secondQuery->getPosition());

        self::assertEquals(count($collection->getQueries()) - 1, count($collectionAfterDelete->getQueries()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDeleteQueryThrowsBadStateExceptionOnNonNamedCollection()
    {
        $query = $this->collectionService->loadQueryDraft(4);
        $this->collectionService->deleteQuery($query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionCreateStruct
     */
    public function testNewCollectionCreateStruct()
    {
        self::assertEquals(
            new CollectionCreateStruct(
                array(
                    'type' => Collection::TYPE_NAMED,
                    'name' => 'New collection',
                )
            ),
            $this->collectionService->newCollectionCreateStruct(
                Collection::TYPE_NAMED,
                'New collection'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct()
    {
        self::assertEquals(
            new CollectionUpdateStruct(),
            $this->collectionService->newCollectionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemCreateStruct
     */
    public function testNewItemCreateStruct()
    {
        self::assertEquals(
            new ItemCreateStruct(
                array(
                    'type' => Item::TYPE_OVERRIDE,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                )
            ),
            $this->collectionService->newItemCreateStruct(Item::TYPE_OVERRIDE, '42', 'ezcontent')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryCreateStruct
     */
    public function testNewQueryCreateStruct()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType(
                'ezcontent_search',
                new QueryTypeHandler(),
                new Configuration('ezcontent_search', '')
            ),
            'new_query'
        );

        self::assertEquals(
            new QueryCreateStruct(
                array(
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'offset' => null,
                        'param' => null,
                    ),
                )
            ),
            $queryCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStruct()
    {
        self::assertEquals(
            new QueryUpdateStruct(),
            $this->collectionService->newQueryUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStructFromQuery()
    {
        $query = $this->collectionService->loadQueryDraft(4);

        self::assertEquals(
            new QueryUpdateStruct(
                array(
                    'identifier' => $query->getIdentifier(),
                    'parameters' => array(
                        'offset' => 0,
                        'param' => null,
                    ),
                )
            ),
            $this->collectionService->newQueryUpdateStruct($query)
        );
    }
}
