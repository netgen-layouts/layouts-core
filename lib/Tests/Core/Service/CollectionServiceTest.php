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
            ->with($this->equalTo(7), $this->equalTo('itemId'));

        $item = $this->collectionService->loadItem(7);

        self::assertInstanceOf(Item::class, $item);
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createNamedCollection
     */
    public function testCreateNamedCollection()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(Collection::TYPE_NAMED);
        $collectionCreateStruct->name = 'New name';

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateCollectionCreateStruct')
            ->with($this->equalTo($collectionCreateStruct));

        $createdCollection = $this->collectionService->createNamedCollection($collectionCreateStruct);

        self::assertInstanceOf(Collection::class, $createdCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createNamedCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateNamedCollectionThrowsBadStateException()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            'My collection'
        );

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateCollectionCreateStruct')
            ->with($this->equalTo($collectionCreateStruct));

        $this->collectionService->createNamedCollection($collectionCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateNamedCollection
     */
    public function testUpdateNamedCollection()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Super cool collection';

        $this->collectionValidatorMock
            ->expects($this->at(0))
            ->method('validateCollectionUpdateStruct')
            ->with($this->equalTo($collectionUpdateStruct));

        $updatedCollection = $this->collectionService->updateNamedCollection($collection, $collectionUpdateStruct);

        self::assertInstanceOf(Collection::class, $updatedCollection);
        self::assertEquals('Super cool collection', $updatedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateNamedCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testUpdateNamedCollectionWithExistingNameThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'My collection';

        $this->collectionService->updateNamedCollection(
            $collection,
            $collectionUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateNamedCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testUpdateNonNamedCollectionThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(1, Collection::STATUS_DRAFT);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'My collection';

        $this->collectionService->updateNamedCollection(
            $collection,
            $collectionUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyNamedCollection
     */
    public function testCopyCollection()
    {
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->copyNamedCollection($collection);

        self::assertInstanceOf(Collection::class, $copiedCollection);
        self::assertEquals(4, $copiedCollection->getId());
        self::assertRegExp('/^My collection \(copy\) \d+$/', $copiedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyNamedCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCopyNonNamedCollectionThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(2);
        $this->collectionService->copyNamedCollection($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollectionStatus
     */
    public function testCreateCollectionStatus()
    {
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->createCollectionStatus($collection, Collection::STATUS_ARCHIVED);

        self::assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollectionStatus
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateCollectionStatusThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->createCollectionStatus($collection, Collection::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     */
    public function testCreateDraft()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct('New collection');
        $collectionCreateStruct->status = Collection::STATUS_PUBLISHED;
        $collection = $this->collectionService->createNamedCollection($collectionCreateStruct);

        $draftCollection = $this->collectionService->createDraft($collection);

        self::assertInstanceOf(Collection::class, $draftCollection);
        self::assertEquals(Collection::STATUS_DRAFT, $draftCollection->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionIfCollectionIsNotPublished()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct('New collection');
        $collectionCreateStruct->status = Collection::STATUS_DRAFT;
        $collection = $this->collectionService->createNamedCollection($collectionCreateStruct);

        $this->collectionService->createDraft($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_PUBLISHED);
        $this->collectionService->createDraft($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     */
    public function testPublishCollection()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);
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
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_PUBLISHED);
        $this->collectionService->publishCollection($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteNamedCollection
     */
    public function testDeleteCollection()
    {
        $collection = $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);

        $this->collectionService->deleteNamedCollection($collection);

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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteNamedCollection
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteCompleteCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        $this->collectionService->deleteNamedCollection($collection, true);

        $this->collectionService->loadCollection($collection->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteNamedCollection
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testDeleteNonNamedCollectionThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(2);

        $this->collectionService->deleteNamedCollection($collection, true);
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
        $collection = $this->collectionService->loadCollection(2);

        $this->collectionService->addItem($collection, $itemCreateStruct, 5);
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
        self::assertEquals(1, $movedItem->getPosition());
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
            $this->collectionService->loadItem(4),
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
        $query = $this->collectionService->loadQuery(1);

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

        self::assertEquals('new_identifier', $updatedQuery->getIdentifier());
        self::assertEquals(array('param' => 'value2', 'param3' => 'value3'), $updatedQuery->getParameters());
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
        self::assertEquals(1, $movedQuery->getPosition());
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
            $this->collectionService->loadQuery(1),
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
                    'name' => 'New collection',
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
            $this->collectionService->newCollectionCreateStruct('New collection')
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
