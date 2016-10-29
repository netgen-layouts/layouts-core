<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Parameters\ParameterVO;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
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

abstract class CollectionServiceTest extends ServiceTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionValidatorMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionValidatorMock = $this->createMock(CollectionValidator::class);

        $this->collectionService = $this->createCollectionService($this->collectionValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        $this->assertInstanceOf(Collection::class, $collection);
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

        $this->assertInstanceOf(CollectionDraft::class, $collection);
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadSharedCollections
     */
    public function testLoadSharedCollections()
    {
        $collections = $this->collectionService->loadSharedCollections();

        $this->assertNotEmpty($collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(Collection::class, $collection);
            $this->assertTrue($collection->isShared());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     */
    public function testLoadItem()
    {
        $item = $this->collectionService->loadItem(7);

        $this->assertInstanceOf(Item::class, $item);
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

        $this->assertInstanceOf(ItemDraft::class, $item);
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

        $this->assertInstanceOf(Query::class, $query);
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

        $this->assertInstanceOf(QueryDraft::class, $query);
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

        $this->assertInstanceOf(CollectionDraft::class, $createdCollection);
        $this->assertEquals('New name', $createdCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateCollectionThrowsBadStateException()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            Collection::TYPE_MANUAL,
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

        $this->assertInstanceOf(CollectionDraft::class, $updatedCollection);
        $this->assertEquals('Super cool collection', $updatedCollection->getName());
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromManualToDynamic()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_DYNAMIC,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('ezcontent_search'),
                'default'
            )
        );

        $this->assertInstanceOf(CollectionDraft::class, $updatedCollection);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $updatedCollection->getType());
        $this->assertEquals(count($updatedCollection->getItems()), count($collection->getItems()));
        $this->assertCount(1, $updatedCollection->getQueries());
        $this->assertEquals('ezcontent_search', $updatedCollection->getQueries()[0]->getQueryType()->getType());
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

        $this->assertInstanceOf(CollectionDraft::class, $updatedCollection);
        $this->assertEquals(Collection::TYPE_MANUAL, $updatedCollection->getType());
        $this->assertEquals(count($updatedCollection->getItems()), count($collection->getItems()));
        $this->assertCount(0, $updatedCollection->getQueries());

        foreach ($updatedCollection->getItems() as $index => $item) {
            $this->assertEquals($index, $item->getPosition());
        }
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
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->copyCollection($collection);

        $this->assertInstanceOf(Collection::class, $copiedCollection);
        $this->assertEquals(6, $copiedCollection->getId());
        $this->assertNull($copiedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     */
    public function testCopyCollectionWithName()
    {
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->copyCollection($collection, 'New name');

        $this->assertInstanceOf(Collection::class, $copiedCollection);
        $this->assertEquals(6, $copiedCollection->getId());
        $this->assertEquals('New name', $copiedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCopyCollectionWithNameThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->copyCollection($collection, 'My other collection');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     */
    public function testCreateDraft()
    {
        $collection = $this->collectionService->loadCollection(2);
        $draftCollection = $this->collectionService->createDraft($collection);

        $this->assertInstanceOf(CollectionDraft::class, $draftCollection);
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

        $this->assertInstanceOf(Collection::class, $publishedCollection);
        $this->assertEquals(Collection::STATUS_PUBLISHED, $publishedCollection->getStatus());

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

        $this->assertInstanceOf(ItemDraft::class, $createdItem);
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
        $this->assertInstanceOf(ItemDraft::class, $movedItem);
        $this->assertEquals(1, $movedItem->getPosition());
        */

        $secondItem = $this->collectionService->loadItemDraft(2);
        $this->assertEquals(0, $secondItem->getPosition());
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
        $this->assertEquals(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     */
    public function testAddQuery()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
            'new_query'
        );

        $collection = $this->collectionService->loadCollectionDraft(3);

        $createdQuery = $this->collectionService->addQuery(
            $collection,
            $queryCreateStruct,
            1
        );

        $this->assertInstanceOf(QueryDraft::class, $createdQuery);

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        $this->assertEquals(2, $secondQuery->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryInManualCollectionThrowsBadStateException()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
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
            new QueryType('ezcontent_search'),
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
            new QueryType('ezcontent_search'),
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

        $this->assertInstanceOf(QueryDraft::class, $updatedQuery);

        $this->assertEquals('new_identifier', $updatedQuery->getIdentifier());
        $this->assertEquals(
            array(
                'offset' => new ParameterVO(
                    array(
                        'identifier' => 'offset',
                        'parameterType' => $query->getQueryType()->getParameters()['offset'],
                        'value' => '0',
                        'isEmpty' => false,
                    )
                ),
                'param' => new ParameterVO(
                    array(
                        'identifier' => 'param',
                        'parameterType' => $query->getQueryType()->getParameters()['param'],
                        'value' => 'value',
                        'isEmpty' => false,
                    )
                ),
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
        $this->assertInstanceOf(QueryDraft::class, $movedQuery);
        $this->assertEquals(1, $movedQuery->getPosition());
        */

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        $this->assertEquals(0, $secondQuery->getPosition());
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
        $this->assertEquals(0, $secondQuery->getPosition());

        $this->assertEquals(count($collection->getQueries()) - 1, count($collectionAfterDelete->getQueries()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionCreateStruct
     */
    public function testNewCollectionCreateStruct()
    {
        $this->assertEquals(
            new CollectionCreateStruct(
                array(
                    'type' => Collection::TYPE_DYNAMIC,
                    'name' => 'New collection',
                )
            ),
            $this->collectionService->newCollectionCreateStruct(
                Collection::TYPE_DYNAMIC,
                'New collection'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(),
            $this->collectionService->newCollectionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemCreateStruct
     */
    public function testNewItemCreateStruct()
    {
        $this->assertEquals(
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
            new QueryType('ezcontent_search'),
            'new_query'
        );

        $this->assertEquals(
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
        $this->assertEquals(
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

        $this->assertEquals(
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
