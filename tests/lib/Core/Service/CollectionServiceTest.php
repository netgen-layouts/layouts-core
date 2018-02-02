<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use DateTime;
use DateTimeImmutable;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Tests\Collection\Stubs\ItemDefinition;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;

abstract class CollectionServiceTest extends ServiceTestCase
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 3;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        $this->assertFalse($updatedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertEquals(6, $updatedCollection->getOffset());
        $this->assertEquals(3, $updatedCollection->getLimit());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollectionWithNoLimit()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 0;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        $this->assertFalse($updatedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertEquals(6, $updatedCollection->getOffset());
        $this->assertNull($updatedCollection->getLimit());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Only draft collections can be updated.
     */
    public function testUpdateCollectionThrowsBadStateExceptionWithNonDraftCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 0;

        $this->collectionService->updateCollection($collection, $collectionUpdateStruct);
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
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(new ItemDefinition('ezcontent'), Item::TYPE_MANUAL, '66');
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
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(new ItemDefinition('ezcontent'), Item::TYPE_MANUAL, '66');
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
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(new ItemDefinition('ezcontent'), Item::TYPE_MANUAL, '66');
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->addItem($collection, $itemCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateItem
     */
    public function testUpdateItem()
    {
        $itemUpdateStruct = $this->collectionService->newItemUpdateStruct();

        $dateTime = DateTimeImmutable::createFromFormat(DateTime::RFC3339, '2018-02-01T15:00:00+0100');

        $visibilityConfigStruct = new ConfigStruct();
        $visibilityConfigStruct->setParameterValue('visible', true);
        $visibilityConfigStruct->setParameterValue('visible_to', $dateTime);

        $itemUpdateStruct->setConfigStruct('visibility', $visibilityConfigStruct);

        $item = $this->collectionService->loadItemDraft(1);

        $updatedItem = $this->collectionService->updateItem($item, $itemUpdateStruct);

        $this->assertFalse($updatedItem->isPublished());
        $this->assertInstanceOf(Item::class, $updatedItem);

        $this->assertTrue($updatedItem->hasConfig('visibility'));
        $visibilityConfig = $updatedItem->getConfig('visibility');

        $this->assertInstanceOf(Config::class, $visibilityConfig);
        $this->assertTrue($visibilityConfig->getParameter('visible')->getValue());
        $this->assertNull($visibilityConfig->getParameter('visible_from')->getValue());
        $this->assertEquals($dateTime, $visibilityConfig->getParameter('visible_to')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "item" has an invalid state. Only draft items can be updated.
     */
    public function testUpdateItemThrowsBadStateExceptionWithNonDraftItem()
    {
        $itemUpdateStruct = $this->collectionService->newItemUpdateStruct();
        $item = $this->collectionService->loadItem(4);

        $this->collectionService->updateItem($item, $itemUpdateStruct);
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
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItems
     */
    public function testDeleteItems()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $collection = $this->collectionService->deleteItems($collection);

        $this->assertCount(0, $collection->getItems());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItems
     */
    public function testDeleteItemsWithSpecificItemType()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            new ItemDefinition('ezcontent'),
            Item::TYPE_OVERRIDE,
            66
        );

        $this->collectionService->addItem($collection, $itemCreateStruct);

        $collection = $this->collectionService->deleteItems($collection, Item::TYPE_OVERRIDE);

        $this->assertCount(3, $collection->getManualItems());
        $this->assertCount(0, $collection->getOverrideItems());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItems
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Only items in draft collections can be deleted.
     */
    public function testDeleteItemsThrowsBadStateExceptionWithNonDraftCollection()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->deleteItems($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItems
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "itemType" has an invalid state. Provided item type is not valid.
     */
    public function testDeleteItemsThrowsBadStateExceptionWithInvalidItemType()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $this->collectionService->deleteItems($collection, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQueryTranslations
     */
    public function testUpdateQuery()
    {
        $query = $this->collectionService->loadQueryDraft(2, array('en'));

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('hr');

        $queryUpdateStruct->setParameterValue('param', 'new_value');
        $queryUpdateStruct->setParameterValue('param2', 3);

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        $this->assertFalse($updatedQuery->isPublished());
        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertEquals('ezcontent_search', $updatedQuery->getQueryType()->getType());

        $this->assertNull($updatedQuery->getParameter('param')->getValue());
        $this->assertEquals(0, $updatedQuery->getParameter('param2')->getValue());

        $croQuery = $this->collectionService->loadQueryDraft(2, array('hr'));

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        $this->assertNull($croQuery->getParameter('param')->getValue());

        $this->assertEquals(3, $croQuery->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQueryTranslations
     */
    public function testUpdateQueryInMainLocale()
    {
        $query = $this->collectionService->loadQueryDraft(2, array('en'));

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('en');

        $queryUpdateStruct->setParameterValue('param', 'new_value');
        $queryUpdateStruct->setParameterValue('param2', 3);

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        $this->assertFalse($updatedQuery->isPublished());
        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertEquals('ezcontent_search', $updatedQuery->getQueryType()->getType());

        $croQuery = $this->collectionService->loadQueryDraft(2, array('hr'));

        $this->assertEquals('new_value', $updatedQuery->getParameter('param')->getValue());
        $this->assertEquals(3, $updatedQuery->getParameter('param2')->getValue());

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        $this->assertEquals('new_value', $croQuery->getParameter('param')->getValue());

        $this->assertEquals(0, $croQuery->getParameter('param2')->getValue());
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
        $queryUpdateStruct->setParameterValue('param', 'value');
        $queryUpdateStruct->setParameterValue('param2', 3);

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
        $queryUpdateStruct->setParameterValue('param', 'value');
        $queryUpdateStruct->setParameterValue('param2', 3);

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionCreateStruct
     */
    public function testNewCollectionCreateStruct()
    {
        $this->assertEquals(
            new CollectionCreateStruct(
                array(
                    'offset' => 0,
                    'limit' => null,
                    'queryCreateStruct' => new QueryCreateStruct(),
                )
            ),
            $this->collectionService->newCollectionCreateStruct(new QueryCreateStruct())
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(
                array(
                    'offset' => null,
                    'limit' => null,
                )
            ),
            $this->collectionService->newCollectionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithCollection()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(
                array(
                    'offset' => 4,
                    'limit' => 2,
                )
            ),
            $this->collectionService->newCollectionUpdateStruct(
                $this->collectionService->loadCollectionDraft(3)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithUnlimitedCollection()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(
                array(
                    'offset' => 0,
                    'limit' => 0,
                )
            ),
            $this->collectionService->newCollectionUpdateStruct(
                $this->collectionService->loadCollectionDraft(1)
            )
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
                    'definition' => new ItemDefinition('ezcontent'),
                    'type' => Item::TYPE_OVERRIDE,
                    'value' => '42',
                )
            ),
            $this->collectionService->newItemCreateStruct(new ItemDefinition('ezcontent'), Item::TYPE_OVERRIDE, '42')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemUpdateStruct
     */
    public function testNewItemUpdateStruct()
    {
        $itemUpdateStruct = new ItemUpdateStruct();

        $this->assertEquals(
            $itemUpdateStruct,
            $this->collectionService->newItemUpdateStruct()
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
                        'param' => null,
                        'param2' => null,
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
                        'param' => null,
                        'param2' => 0,
                    ),
                )
            ),
            $this->collectionService->newQueryUpdateStruct('en', $query)
        );
    }
}
