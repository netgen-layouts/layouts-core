<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;

abstract class CollectionServiceTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService(
            $this->createMock(CollectionValidator::class)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        $this->assertTrue($collection->isPublished());
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "999999"
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

        $this->assertFalse($collection->isPublished());
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollectionDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "999999"
     */
    public function testLoadCollectionDraftThrowsNotFoundException()
    {
        $this->collectionService->loadCollectionDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     */
    public function testLoadItem()
    {
        $item = $this->collectionService->loadItem(7);

        $this->assertTrue($item->isPublished());
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find item with identifier "999999"
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

        $this->assertFalse($item->isPublished());
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItemDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find item with identifier "999999"
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

        $this->assertTrue($query->isPublished());
        $this->assertInstanceOf(Query::class, $query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "999999"
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

        $this->assertFalse($query->isPublished());
        $this->assertInstanceOf(Query::class, $query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQueryDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "999999"
     */
    public function testLoadQueryDraftThrowsNotFoundException()
    {
        $this->collectionService->loadQueryDraft(999999);
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
                new QueryType('ezcontent_search')
            )
        );

        $this->assertFalse($updatedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $updatedCollection);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $updatedCollection->getType());
        $this->assertEquals(count($updatedCollection->getItems()), count($collection->getItems()));
        $this->assertInstanceOf(Query::class, $updatedCollection->getQuery());
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

        $this->assertFalse($updatedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $updatedCollection);
        $this->assertEquals(Collection::TYPE_MANUAL, $updatedCollection->getType());
        $this->assertEquals(count($collection->getItems()), count($updatedCollection->getItems()));
        $this->assertNull($updatedCollection->getQuery());

        foreach ($updatedCollection->getItems() as $index => $item) {
            $this->assertEquals($index, $item->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Type can be changed only for draft collections.
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionWithNonDraftCollection()
    {
        $collection = $this->collectionService->loadCollection(4);

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_MANUAL
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "newType" has an invalid state. New collection type must be manual or dynamic.
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionWithInvalidType()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->changeCollectionType(
            $collection,
            999,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('ezcontent_search')
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "queryCreateStruct" has an invalid state. Query create struct must be defined when converting to dynamic collection.
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

        $this->assertFalse($createdItem->isPublished());
        $this->assertInstanceOf(Item::class, $createdItem);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Items can only be added to draft collections.
     */
    public function testAddItemThrowsBadStateExceptionWithNonDraftCollection()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollection(4);

        $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
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
        $movedItem = $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            1
        );

        $this->assertFalse($movedItem->isPublished());
        $this->assertInstanceOf(Item::class, $movedItem);
        $this->assertEquals(1, $movedItem->getPosition());

        $secondItem = $this->collectionService->loadItemDraft(2);
        $this->assertEquals(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "item" has an invalid state. Only draft items can be moved.
     */
    public function testMoveItemThrowsBadStateExceptionWithNonDraftItem()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItem(4),
            1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveItemThrowsBadStateExceptionWhenPositionIsTooLarge()
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "item" has an invalid state. Only draft items can be deleted.
     */
    public function testDeleteItemThrowsBadStateExceptionWithNonDraftItem()
    {
        $item = $this->collectionService->loadItem(4);
        $this->collectionService->deleteItem($item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQueryTranslations
     */
    public function testUpdateQuery()
    {
        $query = $this->collectionService->loadQueryDraft(2, array('en'));

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('hr');

        $queryUpdateStruct->setParameterValue('offset', 3);
        $queryUpdateStruct->setParameterValue('param', 'new_value');

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        $this->assertFalse($updatedQuery->isPublished());
        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertEquals('ezcontent_search', $updatedQuery->getQueryType()->getType());

        $this->assertEquals(0, $updatedQuery->getParameter('offset')->getValue());
        $this->assertNull($updatedQuery->getParameter('param')->getValue());

        $croQuery = $this->collectionService->loadQueryDraft(2, array('hr'));

        $this->assertEquals(3, $croQuery->getParameter('offset')->getValue());

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        $this->assertNull($croQuery->getParameter('param')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQueryTranslations
     */
    public function testUpdateQueryInMainLocale()
    {
        $query = $this->collectionService->loadQueryDraft(2, array('en'));

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('en');

        $queryUpdateStruct->setParameterValue('offset', 3);
        $queryUpdateStruct->setParameterValue('param', 'new_value');

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        $this->assertFalse($updatedQuery->isPublished());
        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertEquals('ezcontent_search', $updatedQuery->getQueryType()->getType());

        $croQuery = $this->collectionService->loadQueryDraft(2, array('hr'));

        $this->assertEquals(3, $updatedQuery->getParameter('offset')->getValue());
        $this->assertEquals('new_value', $updatedQuery->getParameter('param')->getValue());

        $this->assertEquals(0, $croQuery->getParameter('offset')->getValue());

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        $this->assertEquals('new_value', $croQuery->getParameter('param')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "query" has an invalid state. Only draft queries can be updated.
     */
    public function testUpdateQueryThrowsBadStateExceptionWithNonDraftQuery()
    {
        $query = $this->collectionService->loadQuery(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('en');
        $queryUpdateStruct->setParameterValue('offset', 3);
        $queryUpdateStruct->setParameterValue('param', 'value');

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "query" has an invalid state. Query does not have the specified translation.
     */
    public function testUpdateQueryThrowsBadStateExceptionWithNonExistingLocale()
    {
        $query = $this->collectionService->loadQueryDraft(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('non-existing');
        $queryUpdateStruct->setParameterValue('offset', 3);
        $queryUpdateStruct->setParameterValue('param', 'value');

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
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
            new QueryType('ezcontent_search')
        );

        $this->assertEquals(
            new QueryCreateStruct(
                array(
                    'queryType' => new QueryType('ezcontent_search'),
                    'parameterValues' => array(
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
            new QueryUpdateStruct(
                array(
                    'locale' => 'en',
                )
            ),
            $this->collectionService->newQueryUpdateStruct('en')
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
                    'locale' => 'en',
                    'parameterValues' => array(
                        'offset' => 0,
                        'param' => null,
                    ),
                )
            ),
            $this->collectionService->newQueryUpdateStruct('en', $query)
        );
    }
}
