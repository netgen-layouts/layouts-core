<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Values\QueryCreateStruct;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;

abstract class CollectionServiceTest extends ServiceTest
{
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
        $this->collectionValidatorMock = $this->getMockBuilder(CollectionValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionService = $this->createCollectionService($this->collectionValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollection()
    {
        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateId')
            ->with($this->equalTo(3), $this->equalTo('collectionId'));

        $collection = $this->collectionService->loadCollection(3);

        self::assertInstanceOf(Collection::class, $collection);

        self::assertEquals(3, $collection->getId());
        self::assertEquals('My collection', $collection->getName());
        self::assertEquals(Collection::TYPE_NAMED, $collection->getType());
        self::assertEquals(Collection::STATUS_PUBLISHED, $collection->getStatus());

        self::assertNotEmpty($collection->getItems());
        foreach ($collection->getItems() as $item) {
            self::assertInstanceOf(Item::class, $item);
        }

        self::assertEquals(
            count($collection->getItems()),
            count($collection->getManualItems()) + count($collection->getOverrideItems())
        );

        self::assertNotEmpty($collection->getManualItems());
        foreach ($collection->getManualItems() as $item) {
            self::assertInstanceOf(Item::class, $item);
            self::assertEquals(Item::TYPE_MANUAL, $item->getType());
        }

        self::assertNotEmpty($collection->getOverrideItems());
        foreach ($collection->getOverrideItems() as $item) {
            self::assertInstanceOf(Item::class, $item);
            self::assertEquals(Item::TYPE_OVERRIDE, $item->getType());
        }

        self::assertNotEmpty($collection->getQueries());
        foreach ($collection->getQueries() as $query) {
            self::assertInstanceOf(Query::class, $query);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionService->loadCollection(999999);
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
        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateId')
            ->with($this->equalTo(1), $this->equalTo('itemId'));

        $item = $this->collectionService->loadItem(1);

        self::assertInstanceOf(Item::class, $item);

        self::assertEquals(1, $item->getId());
        self::assertEquals(1, $item->getCollectionId());
        self::assertEquals(Item::TYPE_MANUAL, $item->getType());
        self::assertEquals(0, $item->getPosition());
        self::assertEquals('70', $item->getValueId());
        self::assertEquals('ezcontent', $item->getValueType());
        self::assertEquals(Collection::STATUS_PUBLISHED, $item->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     */
    public function testLoadQuery()
    {
        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateId')
            ->with($this->equalTo(1), $this->equalTo('queryId'));

        $query = $this->collectionService->loadQuery(1);

        self::assertInstanceOf(Query::class, $query);

        self::assertEquals(1, $query->getId());
        self::assertEquals(2, $query->getCollectionId());
        self::assertEquals('ezcontent_search', $query->getType());
        self::assertEquals(0, $query->getPosition());
        self::assertEquals('default', $query->getIdentifier());
        self::assertEquals(array('param' => 'value'), $query->getParameters());
        self::assertEquals(Collection::STATUS_PUBLISHED, $query->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionService->loadQuery(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(Collection::TYPE_DYNAMIC);

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateCollectionCreateStruct')
            ->with($this->equalTo($collectionCreateStruct));

        $createdCollection = $this->collectionService->createCollection($collectionCreateStruct);

        self::assertInstanceOf(Collection::class, $createdCollection);

        self::assertEquals(4, $createdCollection->getId());
        self::assertNull($createdCollection->getName());
        self::assertEquals(Collection::TYPE_DYNAMIC, $createdCollection->getType());
        self::assertEquals(Collection::STATUS_DRAFT, $createdCollection->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateCollectionThrowsBadStateException()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            Collection::TYPE_NAMED,
            'My collection'
        );

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateCollectionCreateStruct')
            ->with($this->equalTo($collectionCreateStruct));

        $this->collectionService->createCollection($collectionCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollection()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Super cool collection';

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateCollectionUpdateStruct')
            ->with($this->equalTo($collectionUpdateStruct));

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        self::assertEquals('Super cool collection', $updatedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateNonNamedCollectionDoesNotChangeName()
    {
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Super cool collection';

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateCollectionUpdateStruct')
            ->with($this->equalTo($collectionUpdateStruct));

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        self::assertNull($updatedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testUpdateCollectionWithExistingNameForNamedCollectionThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'My collection';

        $this->collectionService->updateCollection(
            $collection,
            $collectionUpdateStruct
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

        self::assertEquals(4, $copiedCollection->getId());
        self::assertEquals($collection->getName(), $copiedCollection->getName());
        self::assertEquals($collection->getType(), $copiedCollection->getType());
        self::assertEquals(Collection::STATUS_PUBLISHED, $copiedCollection->getStatus());

        self::assertEquals(count($collection->getItems()), count($copiedCollection->getItems()));
        self::assertEquals(count($collection->getQueries()), count($copiedCollection->getQueries()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     */
    public function testCopyNamedCollectionCollection()
    {
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->copyCollection($collection);

        self::assertInstanceOf(Collection::class, $copiedCollection);

        self::assertEquals(4, $copiedCollection->getId());
        self::assertRegExp('/^My collection \(copy\) \d+$/', $copiedCollection->getName());
        self::assertEquals($collection->getType(), $copiedCollection->getType());
        self::assertEquals(Collection::STATUS_PUBLISHED, $copiedCollection->getStatus());

        self::assertEquals(count($collection->getItems()), count($copiedCollection->getItems()));
        self::assertEquals(count($collection->getQueries()), count($copiedCollection->getQueries()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollectionStatus
     */
    public function testCreateCollectionStatus()
    {
        $collection = $this->collectionService->loadCollection(1);
        $copiedCollection = $this->collectionService->createCollectionStatus($collection, Collection::STATUS_ARCHIVED);

        self::assertInstanceOf(Collection::class, $copiedCollection);

        self::assertEquals($collection->getId(), $copiedCollection->getId());
        self::assertEquals($collection->getName(), $copiedCollection->getName());
        self::assertEquals($collection->getType(), $copiedCollection->getType());
        self::assertEquals(Collection::STATUS_ARCHIVED, $copiedCollection->getStatus());

        self::assertEquals(count($collection->getItems()), count($copiedCollection->getItems()));
        self::assertEquals(count($collection->getQueries()), count($copiedCollection->getQueries()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollectionStatus
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateCollectionStatusThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(1);
        $this->collectionService->createCollectionStatus($collection, Collection::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     */
    public function testPublishCollection()
    {
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);
        $publishedCollection = $this->collectionService->publishCollection($collection);

        self::assertInstanceOf(Collection::class, $publishedCollection);
        self::assertEquals(Collection::STATUS_PUBLISHED, $publishedCollection->getStatus());

        $archivedCollection = $this->collectionService->loadCollection($collection->getId(), Collection::STATUS_ARCHIVED);
        self::assertInstanceOf(Collection::class, $archivedCollection);

        try {
            $this->collectionService->loadCollection($collection->getId(), Collection::STATUS_DRAFT);
            self::fail('Draft collection still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testPublishCollectionThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_PUBLISHED);
        $this->collectionService->publishCollection($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteCollection
     */
    public function testDeleteCollection()
    {
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $this->collectionService->deleteCollection($collection);

        try {
            $this->collectionService->loadCollection($collection->getId(), Collection::STATUS_DRAFT);
            self::fail('Draft collection still exists after deleting it');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $publishedCollection = $this->collectionService->loadCollection($collection->getId());
        self::assertInstanceOf(Collection::class, $publishedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteCollection
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteCompleteCollection()
    {
        $collection = $this->collectionService->loadCollection(1);

        $this->collectionService->deleteCollection($collection, true);

        $this->collectionService->loadCollection($collection->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     */
    public function testAddItem()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validatePosition')
            ->with($this->equalTo(1), $this->equalTo('position'));

        $this->collectionValidatorMock
            ->expects($this->at(1))
            ->method('validateItemCreateStruct')
            ->with($this->equalTo($itemCreateStruct));

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );

        self::assertInstanceOf(Item::class, $createdItem);

        self::assertEquals(12, $createdItem->getId());
        self::assertEquals(1, $createdItem->getCollectionId());
        self::assertEquals(Item::TYPE_MANUAL, $createdItem->getType());
        self::assertEquals(1, $createdItem->getPosition());
        self::assertEquals('66', $createdItem->getValueId());
        self::assertEquals('ezcontent', $createdItem->getValueType());
        self::assertEquals(Collection::STATUS_DRAFT, $createdItem->getStatus());

        $secondItem = $this->collectionService->loadItem(2, Collection::STATUS_DRAFT);
        self::assertEquals(2, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddOverrideItemInManualCollectionThrowsBadStateException()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_OVERRIDE, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $this->collectionService->addItem($collection, $itemCreateStruct, 1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddItemToDynamicCollectionWithExistingPositionThrowsBadStateException()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_OVERRIDE, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollection(2, Collection::STATUS_DRAFT);

        $this->collectionService->addItem($collection, $itemCreateStruct, 5);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testAddItemToDynamicCollectionWithWithNoPositionThrowsInvalidArgumentException()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_OVERRIDE, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollection(2, Collection::STATUS_DRAFT);

        $this->collectionService->addItem($collection, $itemCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $this->collectionService->addItem($collection, $itemCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     */
    public function testMoveItem()
    {
        $this->collectionValidatorMock
            ->expects($this->once())
            ->method('validatePosition')
            ->with($this->equalTo(1), $this->equalTo('position'));

        $movedItem = $this->collectionService->moveItem(
            $this->collectionService->loadItem(1, Collection::STATUS_DRAFT),
            1
        );

        /*
        self::assertInstanceOf(Item::class, $movedItem);

        self::assertEquals(1, $movedItem->getId());
        self::assertEquals(1, $movedItem->getCollectionId());
        self::assertEquals(Item::TYPE_MANUAL, $movedItem->getType());
        self::assertEquals(1, $movedItem->getPosition());
        self::assertEquals('70', $movedItem->getValueId());
        self::assertEquals('ezcontent', $movedItem->getValueType());
        self::assertEquals(Collection::STATUS_DRAFT, $movedItem->getStatus());
        */

        $secondItem = $this->collectionService->loadItem(2, Collection::STATUS_DRAFT);
        self::assertEquals(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveItemThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItem(1, Collection::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveItemInDynamicCollectionWithExistingPositionThrowsBadStateException()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItem(4, Collection::STATUS_DRAFT),
            5
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItem()
    {
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $item = $this->collectionService->loadItem(1, Collection::STATUS_DRAFT);
        $this->collectionService->deleteItem($item);

        $collectionAfterDelete = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        try {
            $this->collectionService->loadItem($item->getId(), Collection::STATUS_DRAFT);
            self::fail('Item still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondItem = $this->collectionService->loadItem(2, Collection::STATUS_DRAFT);
        self::assertEquals(0, $secondItem->getPosition());

        self::assertEquals(count($collection->getItems()) - 1, count($collectionAfterDelete->getItems()));
        self::assertEquals(count($collection->getManualItems()) - 1, count($collectionAfterDelete->getManualItems()));
        self::assertEquals(count($collection->getOverrideItems()), count($collectionAfterDelete->getOverrideItems()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     */
    public function testAddQuery()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct('new_query', 'ezcontent_search');
        $queryCreateStruct->setParameter('param2', 'value2');

        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validatePosition')
            ->with($this->equalTo(1), $this->equalTo('position'));

        $this->collectionValidatorMock
            ->expects($this->at(1))
            ->method('validateQueryCreateStruct')
            ->with($this->equalTo($queryCreateStruct));

        $createdQuery = $this->collectionService->addQuery(
            $collection,
            $queryCreateStruct,
            1
        );

        self::assertInstanceOf(Query::class, $createdQuery);

        self::assertEquals(4, $createdQuery->getId());
        self::assertEquals(3, $createdQuery->getCollectionId());
        self::assertEquals('ezcontent_search', $createdQuery->getType());
        self::assertEquals(1, $createdQuery->getPosition());
        self::assertEquals('new_query', $createdQuery->getIdentifier());
        self::assertEquals(array('param2' => 'value2'), $createdQuery->getParameters());
        self::assertEquals(Collection::STATUS_DRAFT, $createdQuery->getStatus());

        $secondQuery = $this->collectionService->loadQuery(3, Collection::STATUS_DRAFT);
        self::assertEquals(2, $secondQuery->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddQueryInManualCollectionThrowsBadStateException()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct('new_query', 'ezcontent_search');
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddQueryWithExistingIdentifierThrowsBadStateException()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct('default', 'ezcontent_search');
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct('new_query', 'ezcontent_search');
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     */
    public function testUpdateQuery()
    {
        $query = $this->collectionService->loadQuery(1, Collection::STATUS_DRAFT);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = 'new_identifier';
        $queryUpdateStruct->setParameter('param', 'value2');
        $queryUpdateStruct->setParameter('param3', 'value3');

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateQueryUpdateStruct')
            ->with($this->equalTo($query), $this->equalTo($queryUpdateStruct));

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        self::assertInstanceOf(Query::class, $updatedQuery);

        self::assertEquals(1, $updatedQuery->getId());
        self::assertEquals(2, $updatedQuery->getCollectionId());
        self::assertEquals('ezcontent_search', $updatedQuery->getType());
        self::assertEquals(0, $updatedQuery->getPosition());
        self::assertEquals('new_identifier', $updatedQuery->getIdentifier());
        self::assertEquals(array('param' => 'value2', 'param3' => 'value3'), $updatedQuery->getParameters());
        self::assertEquals(Collection::STATUS_DRAFT, $updatedQuery->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testUpdateQueryWithExistingIdentifierThrowsBadStateException()
    {
        $query = $this->collectionService->loadQuery(2, Collection::STATUS_DRAFT);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = 'featured';

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     */
    public function testMoveQuery()
    {
        $this->collectionValidatorMock
            ->expects($this->once())
            ->method('validatePosition')
            ->with($this->equalTo(1), $this->equalTo('position'));

        $movedQuery = $this->collectionService->moveQuery(
            $this->collectionService->loadQuery(2, Collection::STATUS_DRAFT),
            1
        );

        /*
        self::assertInstanceOf(Query::class, $movedQuery);

        self::assertEquals(2, $movedQuery->getId());
        self::assertEquals(3, $movedQuery->getCollectionId());
        self::assertEquals('ezcontent_search', $movedQuery->getType());
        self::assertEquals(1, $movedQuery->getPosition());
        self::assertEquals('default', $movedQuery->getIdentifier());
        self::assertEquals(array('param' => 'value'), $movedQuery->getParameters());
        self::assertEquals(Collection::STATUS_DRAFT, $movedQuery->getStatus());
        */

        $secondQuery = $this->collectionService->loadQuery(3, Collection::STATUS_DRAFT);
        self::assertEquals(0, $secondQuery->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveQueryThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQuery(1, Collection::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteQuery
     */
    public function testDeleteQuery()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $query = $this->collectionService->loadQuery(2, Collection::STATUS_DRAFT);
        $this->collectionService->deleteQuery($query);

        $collectionAfterDelete = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        try {
            $this->collectionService->loadQuery($query->getId(), Collection::STATUS_DRAFT);
            self::fail('Query still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondQuery = $this->collectionService->loadQuery(3, Collection::STATUS_DRAFT);
        self::assertEquals(0, $secondQuery->getPosition());

        self::assertEquals(count($collection->getQueries()) - 1, count($collectionAfterDelete->getQueries()));
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
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionService->newCollectionCreateStruct(Collection::TYPE_NAMED, 'New collection')
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
        self::assertEquals(
            new QueryCreateStruct(
                array(
                    'identifier' => 'new_query',
                    'type' => 'ezcontent_search',
                )
            ),
            $this->collectionService->newQueryCreateStruct('new_query', 'ezcontent_search')
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
}
