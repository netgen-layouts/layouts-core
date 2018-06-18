<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use DateTimeImmutable;
use DateTimeZone;
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
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;

abstract class CollectionServiceTest extends ServiceTestCase
{
    use ExportObjectVarsTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollection(): void
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
    public function testLoadCollectionThrowsNotFoundException(): void
    {
        $this->collectionService->loadCollection(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollectionDraft
     */
    public function testLoadCollectionDraft(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $this->assertTrue($collection->isDraft());
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollectionDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "999999"
     */
    public function testLoadCollectionDraftThrowsNotFoundException(): void
    {
        $this->collectionService->loadCollectionDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollection(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 3;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        $this->assertTrue($updatedCollection->isDraft());
        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertSame(6, $updatedCollection->getOffset());
        $this->assertSame(3, $updatedCollection->getLimit());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollectionWithNoLimit(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 0;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        $this->assertTrue($updatedCollection->isDraft());
        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertSame(6, $updatedCollection->getOffset());
        $this->assertNull($updatedCollection->getLimit());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Only draft collections can be updated.
     */
    public function testUpdateCollectionThrowsBadStateExceptionWithNonDraftCollection(): void
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
    public function testLoadItem(): void
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
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItemDraft
     */
    public function testLoadItemDraft(): void
    {
        $item = $this->collectionService->loadItemDraft(7);

        $this->assertTrue($item->isDraft());
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItemDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find item with identifier "999999"
     */
    public function testLoadItemDraftThrowsNotFoundException(): void
    {
        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     */
    public function testLoadQuery(): void
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
    public function testLoadQueryThrowsNotFoundException(): void
    {
        $this->collectionService->loadQuery(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQueryDraft
     */
    public function testLoadQueryDraft(): void
    {
        $query = $this->collectionService->loadQueryDraft(2);

        $this->assertTrue($query->isDraft());
        $this->assertInstanceOf(Query::class, $query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQueryDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "999999"
     */
    public function testLoadQueryDraftThrowsNotFoundException(): void
    {
        $this->collectionService->loadQueryDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromManualToDynamic(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_DYNAMIC,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('my_query_type')
            )
        );

        $this->assertTrue($updatedCollection->isDraft());
        $this->assertInstanceOf(Collection::class, $updatedCollection);
        $this->assertSame(Collection::TYPE_DYNAMIC, $updatedCollection->getType());
        $this->assertSame(count($updatedCollection->getItems()), count($collection->getItems()));
        $this->assertInstanceOf(Query::class, $updatedCollection->getQuery());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromDynamicToManual(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_MANUAL
        );

        $this->assertTrue($updatedCollection->isDraft());
        $this->assertInstanceOf(Collection::class, $updatedCollection);
        $this->assertSame(Collection::TYPE_MANUAL, $updatedCollection->getType());
        $this->assertSame(count($collection->getItems()), count($updatedCollection->getItems()));
        $this->assertNull($updatedCollection->getQuery());

        foreach ($updatedCollection->getItems() as $index => $item) {
            $this->assertSame($index, $item->getPosition());
        }

        $this->assertSame(0, $updatedCollection->getOffset());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Type can be changed only for draft collections.
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionWithNonDraftCollection(): void
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
    public function testChangeCollectionTypeThrowsBadStateExceptionWithInvalidType(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->changeCollectionType(
            $collection,
            999,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('my_query_type')
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "queryCreateStruct" has an invalid state. Query create struct must be defined when converting to dynamic collection.
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionOnChangingToDynamicCollectionWithoutQueryCreateStruct(): void
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
    public function testAddItem(): void
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            new ItemDefinition(['valueType' => 'my_value_type']),
            Item::TYPE_MANUAL,
            '66'
        );

        $collection = $this->collectionService->loadCollectionDraft(1);

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );

        $this->assertTrue($createdItem->isDraft());
        $this->assertInstanceOf(Item::class, $createdItem);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Items can only be added to draft collections.
     */
    public function testAddItemThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            new ItemDefinition(['valueType' => 'my_value_type']),
            Item::TYPE_MANUAL,
            '66'
        );

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
    public function testAddItemThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            new ItemDefinition(['valueType' => 'my_value_type']),
            Item::TYPE_MANUAL,
            '66'
        );

        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->addItem($collection, $itemCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateItem
     */
    public function testUpdateItem(): void
    {
        $itemUpdateStruct = $this->collectionService->newItemUpdateStruct();

        $dateTime = new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey'));

        $visibilityConfigStruct = new ConfigStruct();
        $visibilityConfigStruct->setParameterValue('visibility_status', Item::VISIBILITY_SCHEDULED);
        $visibilityConfigStruct->setParameterValue('visible_to', $dateTime);

        $itemUpdateStruct->setConfigStruct('visibility', $visibilityConfigStruct);

        $item = $this->collectionService->loadItemDraft(1);

        $updatedItem = $this->collectionService->updateItem($item, $itemUpdateStruct);

        $this->assertTrue($updatedItem->isDraft());
        $this->assertInstanceOf(Item::class, $updatedItem);

        $this->assertTrue($updatedItem->hasConfig('visibility'));
        $visibilityConfig = $updatedItem->getConfig('visibility');

        $this->assertInstanceOf(Config::class, $visibilityConfig);
        $this->assertSame(Item::VISIBILITY_SCHEDULED, $visibilityConfig->getParameter('visibility_status')->getValue());
        $this->assertNull($visibilityConfig->getParameter('visible_from')->getValue());

        $visibleTo = $visibilityConfig->getParameter('visible_to')->getValue();

        $this->assertInstanceOf(DateTimeImmutable::class, $visibleTo);
        $this->assertSame('2018-02-01 15:00:00', $visibleTo->format('Y-m-d H:i:s'));
        $this->assertSame('Antarctica/Casey', $visibleTo->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "item" has an invalid state. Only draft items can be updated.
     */
    public function testUpdateItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $itemUpdateStruct = $this->collectionService->newItemUpdateStruct();
        $item = $this->collectionService->loadItem(4);

        $this->collectionService->updateItem($item, $itemUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     */
    public function testMoveItem(): void
    {
        $movedItem = $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            1
        );

        $this->assertTrue($movedItem->isDraft());
        $this->assertInstanceOf(Item::class, $movedItem);
        $this->assertSame(1, $movedItem->getPosition());

        $secondItem = $this->collectionService->loadItemDraft(2);
        $this->assertSame(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "item" has an invalid state. Only draft items can be moved.
     */
    public function testMoveItemThrowsBadStateExceptionWithNonDraftItem(): void
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
    public function testMoveItemThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItem(): void
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
        $this->assertSame(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "item" has an invalid state. Only draft items can be deleted.
     */
    public function testDeleteItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $item = $this->collectionService->loadItem(4);
        $this->collectionService->deleteItem($item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItems
     */
    public function testDeleteItems(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $collection = $this->collectionService->deleteItems($collection);

        $this->assertCount(0, $collection->getItems());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItems
     */
    public function testDeleteItemsWithSpecificItemType(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            new ItemDefinition(['valueType' => 'my_value_type']),
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
    public function testDeleteItemsThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->deleteItems($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItems
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "itemType" has an invalid state. Provided item type is not valid.
     */
    public function testDeleteItemsThrowsBadStateExceptionWithInvalidItemType(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $this->collectionService->deleteItems($collection, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQueryTranslations
     */
    public function testUpdateQuery(): void
    {
        $query = $this->collectionService->loadQueryDraft(2, ['en']);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('hr');

        $queryUpdateStruct->setParameterValue('param', 'new_value');
        $queryUpdateStruct->setParameterValue('param2', 3);

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        $this->assertTrue($updatedQuery->isDraft());
        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertSame('my_query_type', $updatedQuery->getQueryType()->getType());

        $this->assertNull($updatedQuery->getParameter('param')->getValue());
        $this->assertNull($updatedQuery->getParameter('param2')->getValue());

        $croQuery = $this->collectionService->loadQueryDraft(2, ['hr']);

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        $this->assertNull($croQuery->getParameter('param')->getValue());

        $this->assertSame(3, $croQuery->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQueryTranslations
     */
    public function testUpdateQueryInMainLocale(): void
    {
        $query = $this->collectionService->loadQueryDraft(2, ['en']);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('en');

        $queryUpdateStruct->setParameterValue('param', 'new_value');
        $queryUpdateStruct->setParameterValue('param2', 3);

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        $this->assertTrue($updatedQuery->isDraft());
        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertSame('my_query_type', $updatedQuery->getQueryType()->getType());

        $croQuery = $this->collectionService->loadQueryDraft(2, ['hr']);

        $this->assertSame('new_value', $updatedQuery->getParameter('param')->getValue());
        $this->assertSame(3, $updatedQuery->getParameter('param2')->getValue());

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        $this->assertSame('new_value', $croQuery->getParameter('param')->getValue());

        $this->assertNull($croQuery->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "query" has an invalid state. Only draft queries can be updated.
     */
    public function testUpdateQueryThrowsBadStateExceptionWithNonDraftQuery(): void
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
    public function testUpdateQueryThrowsBadStateExceptionWithNonExistingLocale(): void
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
    public function testNewCollectionCreateStruct(): void
    {
        $queryCreateStruct = new QueryCreateStruct();
        $struct = $this->collectionService->newCollectionCreateStruct($queryCreateStruct);

        $this->assertInstanceOf(CollectionCreateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => 0,
                'limit' => null,
                'queryCreateStruct' => $queryCreateStruct,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct();

        $this->assertInstanceOf(CollectionUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => null,
                'limit' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithCollection(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(3)
        );

        $this->assertInstanceOf(CollectionUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => 4,
                'limit' => 2,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithUnlimitedCollection(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(1)
        );

        $this->assertInstanceOf(CollectionUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => 0,
                'limit' => 0,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemCreateStruct
     */
    public function testNewItemCreateStruct(): void
    {
        $itemDefinition = new ItemDefinition();
        $struct = $this->collectionService->newItemCreateStruct($itemDefinition, Item::TYPE_OVERRIDE, '42');

        $this->assertInstanceOf(ItemCreateStruct::class, $struct);

        $this->assertSame(
            [
                'definition' => $itemDefinition,
                'value' => '42',
                'type' => Item::TYPE_OVERRIDE,
                'configStructs' => [],
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemUpdateStruct
     */
    public function testNewItemUpdateStruct(): void
    {
        $struct = $this->collectionService->newItemUpdateStruct();

        $this->assertInstanceOf(ItemUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'configStructs' => [],
            ],
            $this->exportObjectVars($struct, true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemUpdateStruct
     */
    public function testNewItemUpdateStructFromItem(): void
    {
        $item = $this->collectionService->loadItemDraft(1);
        $struct = $this->collectionService->newItemUpdateStruct($item);

        $this->assertInstanceOf(ItemUpdateStruct::class, $struct);

        $this->assertArrayHasKey('visibility', $struct->getConfigStructs());
        $this->assertInstanceOf(ConfigStruct::class, $struct->getConfigStruct('visibility'));

        $this->assertSame(
            [
                'configStructs' => [
                    'visibility' => [
                        'parameterValues' => [
                            'visibility_status' => null,
                            'visible_from' => null,
                            'visible_to' => null,
                        ],
                    ],
                ],
            ],
            $this->exportObjectVars($struct, true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryCreateStruct
     */
    public function testNewQueryCreateStruct(): void
    {
        $queryType = new QueryType('my_query_type');

        $struct = $this->collectionService->newQueryCreateStruct($queryType);

        $this->assertInstanceOf(QueryCreateStruct::class, $struct);

        $this->assertSame(
            [
                'queryType' => $queryType,
                'parameterValues' => [
                    'param' => null,
                    'param2' => null,
                ],
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStruct(): void
    {
        $struct = $this->collectionService->newQueryUpdateStruct('en');

        $this->assertInstanceOf(QueryUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [],
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStructFromQuery(): void
    {
        $query = $this->collectionService->loadQueryDraft(4);
        $struct = $this->collectionService->newQueryUpdateStruct('en', $query);

        $this->assertInstanceOf(QueryUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [
                    'param' => null,
                    'param2' => null,
                ],
            ],
            $this->exportObjectVars($struct)
        );
    }
}
