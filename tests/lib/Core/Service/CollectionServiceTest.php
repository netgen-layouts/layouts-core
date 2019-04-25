<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class CollectionServiceTest extends CoreTestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::__construct
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollection(): void
    {
        $collection = $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        self::assertTrue($collection->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollectionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadCollection(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::__construct
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadCollectionDraft
     */
    public function testLoadCollectionDraft(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        self::assertTrue($collection->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadCollectionDraft
     */
    public function testLoadCollectionDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadCollectionDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollection(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 3;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        self::assertTrue($updatedCollection->isDraft());
        self::assertSame(6, $updatedCollection->getOffset());
        self::assertSame(3, $updatedCollection->getLimit());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollectionWithNoLimit(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 0;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        self::assertTrue($updatedCollection->isDraft());
        self::assertSame(6, $updatedCollection->getOffset());
        self::assertNull($updatedCollection->getLimit());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollectionThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Only draft collections can be updated.');

        $collection = $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 0;

        $this->collectionService->updateCollection($collection, $collectionUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadItem
     */
    public function testLoadItem(): void
    {
        $item = $this->collectionService->loadItem(7);

        self::assertTrue($item->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadItem
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find item with identifier "999999"');

        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadItemDraft
     */
    public function testLoadItemDraft(): void
    {
        $item = $this->collectionService->loadItemDraft(7);

        self::assertTrue($item->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadItemDraft
     */
    public function testLoadItemDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find item with identifier "999999"');

        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadQuery
     */
    public function testLoadQuery(): void
    {
        $query = $this->collectionService->loadQuery(2);

        self::assertTrue($query->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadQuery
     */
    public function testLoadQueryThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "999999"');

        $this->collectionService->loadQuery(999999);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadQueryDraft
     */
    public function testLoadQueryDraft(): void
    {
        $query = $this->collectionService->loadQueryDraft(2);

        self::assertTrue($query->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::loadQueryDraft
     */
    public function testLoadQueryDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "999999"');

        $this->collectionService->loadQueryDraft(999999);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromManualToDynamic(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromDynamicToManual(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Type can be changed only for draft collections.');

        $collection = $this->collectionService->loadCollection(Uuid::fromString('08937ca0-18f4-5806-84df-8c132c36cabe'));

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_MANUAL
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionWithInvalidType(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "newType" has an invalid state. New collection type must be manual or dynamic.');

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $this->collectionService->changeCollectionType(
            $collection,
            999,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('my_query_type')
            )
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionOnChangingToDynamicCollectionWithoutQueryCreateStruct(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "queryCreateStruct" has an invalid state. Query create struct must be defined when converting to dynamic collection.');

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_DYNAMIC
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::addItem
     */
    public function testAddItem(): void
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            ItemDefinition::fromArray(['valueType' => 'my_value_type']),
            '66'
        );

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );

        self::assertTrue($createdItem->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::addItem
     */
    public function testAddItemThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Items can only be added to draft collections.');

        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            ItemDefinition::fromArray(['valueType' => 'my_value_type']),
            '66'
        );

        $collection = $this->collectionService->loadCollection(Uuid::fromString('08937ca0-18f4-5806-84df-8c132c36cabe'));

        $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::addItem
     */
    public function testAddItemThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            ItemDefinition::fromArray(['valueType' => 'my_value_type']),
            '66'
        );

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $this->collectionService->addItem($collection, $itemCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateItem
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateItem
     */
    public function testUpdateItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "item" has an invalid state. Only draft items can be updated.');

        $itemUpdateStruct = $this->collectionService->newItemUpdateStruct();
        $item = $this->collectionService->loadItem(4);

        $this->collectionService->updateItem($item, $itemUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::moveItem
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::moveItem
     */
    public function testMoveItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "item" has an invalid state. Only draft items can be moved.');

        $this->collectionService->moveItem(
            $this->collectionService->loadItem(4),
            1
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::moveItem
     */
    public function testMoveItemThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            9999
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItem
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "item" has an invalid state. Only draft items can be deleted.');

        $item = $this->collectionService->loadItem(4);
        $this->collectionService->deleteItem($item);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItems
     */
    public function testDeleteItems(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));
        $collection = $this->collectionService->deleteItems($collection);

        self::assertCount(0, $collection->getItems());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItems
     */
    public function testDeleteItemsThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Only items in draft collections can be deleted.');

        $collection = $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));
        $this->collectionService->deleteItems($collection);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateQueryTranslations
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateQuery
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateQueryTranslations
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateQuery
     */
    public function testUpdateQueryThrowsBadStateExceptionWithNonDraftQuery(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "query" has an invalid state. Only draft queries can be updated.');

        $query = $this->collectionService->loadQuery(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('en');
        $queryUpdateStruct->setParameterValue('param', 'value');
        $queryUpdateStruct->setParameterValue('param2', 3);

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateQuery
     */
    public function testUpdateQueryThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "query" has an invalid state. Query does not have the specified translation.');

        $query = $this->collectionService->loadQueryDraft(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('non-existing');
        $queryUpdateStruct->setParameterValue('param', 'value');
        $queryUpdateStruct->setParameterValue('param2', 3);

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newCollectionCreateStruct
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newCollectionUpdateStruct
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithCollection(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'))
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithUnlimitedCollection(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'))
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newItemCreateStruct
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newItemUpdateStruct
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newItemUpdateStruct
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newQueryCreateStruct
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newQueryUpdateStruct
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
     * @covers \Netgen\Layouts\Core\Service\CollectionService::newQueryUpdateStruct
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
