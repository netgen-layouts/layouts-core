<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\CoreTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

abstract class CollectionServiceTest extends CoreTestCase
{
    use ExportObjectTrait;

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

        self::assertTrue($collection->isPublished());
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

        self::assertTrue($collection->isDraft());
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

        self::assertTrue($updatedCollection->isDraft());
        self::assertSame(6, $updatedCollection->getOffset());
        self::assertSame(3, $updatedCollection->getLimit());
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

        self::assertTrue($updatedCollection->isDraft());
        self::assertSame(6, $updatedCollection->getOffset());
        self::assertNull($updatedCollection->getLimit());
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

        self::assertTrue($item->isPublished());
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

        self::assertTrue($item->isDraft());
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

        self::assertTrue($query->isPublished());
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

        self::assertTrue($query->isDraft());
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

        self::assertTrue($updatedCollection->isDraft());
        self::assertCount(count($collection->getItems()), $updatedCollection->getItems());
        self::assertInstanceOf(Query::class, $updatedCollection->getQuery());
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

        self::assertTrue($updatedCollection->isDraft());
        self::assertCount(count($collection->getItems()), $updatedCollection->getItems());
        self::assertNull($updatedCollection->getQuery());

        foreach ($updatedCollection->getItems() as $index => $item) {
            self::assertSame($index, $item->getPosition());
        }

        self::assertSame(0, $updatedCollection->getOffset());
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
            ItemDefinition::fromArray(['valueType' => 'my_value_type']),
            '66'
        );

        $collection = $this->collectionService->loadCollectionDraft(1);

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );

        self::assertTrue($createdItem->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "collection" has an invalid state. Items can only be added to draft collections.
     */
    public function testAddItemThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            ItemDefinition::fromArray(['valueType' => 'my_value_type']),
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
            ItemDefinition::fromArray(['valueType' => 'my_value_type']),
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

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param2', 42);

        $itemUpdateStruct->setConfigStruct('key', $configStruct);

        $item = $this->collectionService->loadItemDraft(1);

        $updatedItem = $this->collectionService->updateItem($item, $itemUpdateStruct);

        self::assertTrue($updatedItem->isDraft());
        self::assertTrue($updatedItem->hasConfig('key'));

        $itemConfig = $updatedItem->getConfig('key');
        self::assertNull($itemConfig->getParameter('param1')->getValue());
        self::assertSame(42, $itemConfig->getParameter('param2')->getValue());
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

        self::assertTrue($movedItem->isDraft());
        self::assertSame(1, $movedItem->getPosition());

        $secondItem = $this->collectionService->loadItemDraft(2);
        self::assertSame(0, $secondItem->getPosition());
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
        self::assertSame(0, $secondItem->getPosition());
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

        self::assertCount(0, $collection->getItems());
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

        self::assertTrue($updatedQuery->isDraft());
        self::assertSame('my_query_type', $updatedQuery->getQueryType()->getType());

        self::assertNull($updatedQuery->getParameter('param')->getValue());
        self::assertNull($updatedQuery->getParameter('param2')->getValue());

        $croQuery = $this->collectionService->loadQueryDraft(2, ['hr']);

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        self::assertNull($croQuery->getParameter('param')->getValue());

        self::assertSame(3, $croQuery->getParameter('param2')->getValue());
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

        self::assertTrue($updatedQuery->isDraft());
        self::assertSame('my_query_type', $updatedQuery->getQueryType()->getType());

        $croQuery = $this->collectionService->loadQueryDraft(2, ['hr']);

        self::assertSame('new_value', $updatedQuery->getParameter('param')->getValue());
        self::assertSame(3, $updatedQuery->getParameter('param2')->getValue());

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        self::assertSame('new_value', $croQuery->getParameter('param')->getValue());

        self::assertNull($croQuery->getParameter('param2')->getValue());
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
        $queryCreateStruct = new QueryCreateStruct(new QueryType('my_query_type'));
        $struct = $this->collectionService->newCollectionCreateStruct($queryCreateStruct);

        self::assertSame(
            [
                'offset' => 0,
                'limit' => null,
                'queryCreateStruct' => $queryCreateStruct,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct();

        self::assertSame(
            [
                'offset' => null,
                'limit' => null,
            ],
            $this->exportObject($struct)
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

        self::assertSame(
            [
                'offset' => 4,
                'limit' => 2,
            ],
            $this->exportObject($struct)
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

        self::assertSame(
            [
                'offset' => 0,
                'limit' => 0,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemCreateStruct
     */
    public function testNewItemCreateStruct(): void
    {
        $itemDefinition = new ItemDefinition();
        $struct = $this->collectionService->newItemCreateStruct($itemDefinition, '42');

        self::assertSame(
            [
                'definition' => $itemDefinition,
                'value' => '42',
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemUpdateStruct
     */
    public function testNewItemUpdateStruct(): void
    {
        $struct = $this->collectionService->newItemUpdateStruct();

        self::assertSame(
            [
                'configStructs' => [],
            ],
            $this->exportObject($struct, true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemUpdateStruct
     */
    public function testNewItemUpdateStructFromItem(): void
    {
        $item = $this->collectionService->loadItemDraft(1);
        $struct = $this->collectionService->newItemUpdateStruct($item);

        self::assertArrayHasKey('key', $struct->getConfigStructs());

        self::assertSame(
            [
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
            ],
            $this->exportObject($struct, true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryCreateStruct
     */
    public function testNewQueryCreateStruct(): void
    {
        $queryType = new QueryType('my_query_type');

        $struct = $this->collectionService->newQueryCreateStruct($queryType);

        self::assertSame(
            [
                'queryType' => $queryType,
                'parameterValues' => [
                    'param' => 'value',
                    'param2' => null,
                ],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStruct(): void
    {
        $struct = $this->collectionService->newQueryUpdateStruct('en');

        self::assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStructFromQuery(): void
    {
        $query = $this->collectionService->loadQueryDraft(4);
        $struct = $this->collectionService->newQueryUpdateStruct('en', $query);

        self::assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [
                    'param' => null,
                    'param2' => null,
                ],
            ],
            $this->exportObject($struct)
        );
    }
}
