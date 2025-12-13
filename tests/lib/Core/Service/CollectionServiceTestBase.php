<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Netgen\Layouts\API\Values\Collection\CollectionType;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Symfony\Component\Uid\Uuid;

use function count;

abstract class CollectionServiceTestBase extends CoreTestCase
{
    use ExportObjectTrait;

    final public function testLoadCollection(): void
    {
        $collection = $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        self::assertTrue($collection->isPublished);
    }

    final public function testLoadCollectionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadCollection(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testLoadCollectionDraft(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        self::assertTrue($collection->isDraft);
    }

    final public function testLoadCollectionDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadCollectionDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testUpdateCollection(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 3;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        self::assertTrue($updatedCollection->isDraft);
        self::assertSame(6, $updatedCollection->offset);
        self::assertSame(3, $updatedCollection->limit);
    }

    final public function testUpdateCollectionWithNoLimit(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 0;

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        self::assertTrue($updatedCollection->isDraft);
        self::assertSame(6, $updatedCollection->offset);
        self::assertNull($updatedCollection->limit);
    }

    final public function testUpdateCollectionThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Only draft collections can be updated.');

        $collection = $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();

        $collectionUpdateStruct->offset = 6;
        $collectionUpdateStruct->limit = 0;

        $this->collectionService->updateCollection($collection, $collectionUpdateStruct);
    }

    final public function testLoadItem(): void
    {
        $item = $this->collectionService->loadItem(Uuid::fromString('89c214a3-204f-5352-85d7-8852b26ab6b0'));

        self::assertTrue($item->isPublished);
    }

    final public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadItem(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testLoadItemDraft(): void
    {
        $item = $this->collectionService->loadItemDraft(Uuid::fromString('89c214a3-204f-5352-85d7-8852b26ab6b0'));

        self::assertTrue($item->isDraft);
    }

    final public function testLoadItemDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadItem(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testLoadQuery(): void
    {
        $query = $this->collectionService->loadQuery(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'));

        self::assertTrue($query->isPublished);
    }

    final public function testLoadQueryThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadQuery(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testLoadQueryDraft(): void
    {
        $query = $this->collectionService->loadQueryDraft(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'));

        self::assertTrue($query->isDraft);
    }

    final public function testLoadQueryDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadQueryDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testLoadSlot(): void
    {
        $slot = $this->collectionService->loadSlot(Uuid::fromString('c63c9523-e579-4dc9-b1d2-f9d12470a014'));

        self::assertTrue($slot->isPublished);
    }

    final public function testLoadSlotThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find slot with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadSlot(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testLoadSlotDraft(): void
    {
        $slot = $this->collectionService->loadSlotDraft(Uuid::fromString('de3a0641-c67f-48e0-96e7-7c83b6735265'));

        self::assertTrue($slot->isDraft);
    }

    final public function testLoadSlotDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find slot with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->collectionService->loadSlot(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    final public function testChangeCollectionTypeFromManualToDynamic(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            CollectionType::Dynamic,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('test_query_type'),
            ),
        );

        self::assertTrue($updatedCollection->isDraft);
        self::assertCount(count($collection->items), $updatedCollection->items);
        self::assertInstanceOf(Query::class, $updatedCollection->query);
    }

    final public function testChangeCollectionTypeFromDynamicToManual(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            CollectionType::Manual,
        );

        self::assertTrue($updatedCollection->isDraft);
        self::assertCount(count($collection->items), $updatedCollection->items);
        self::assertNull($updatedCollection->query);

        foreach ($updatedCollection->items as $index => $item) {
            self::assertSame($index, $item->position);
        }

        self::assertSame(0, $updatedCollection->offset);
    }

    final public function testChangeCollectionTypeThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Type can be changed only for draft collections.');

        $collection = $this->collectionService->loadCollection(Uuid::fromString('08937ca0-18f4-5806-84df-8c132c36cabe'));

        $this->collectionService->changeCollectionType(
            $collection,
            CollectionType::Manual,
        );
    }

    final public function testChangeCollectionTypeThrowsBadStateExceptionOnChangingToDynamicCollectionWithoutQueryCreateStruct(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "queryCreateStruct" has an invalid state. Query create struct must be defined when converting to dynamic collection.');

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $this->collectionService->changeCollectionType(
            $collection,
            CollectionType::Dynamic,
        );
    }

    final public function testAddItem(): void
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            ItemDefinition::fromArray(['valueType' => 'test_value_type']),
            '66',
        );

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1,
        );

        self::assertTrue($createdItem->isDraft);
    }

    final public function testAddItemThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Items can only be added to draft collections.');

        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            ItemDefinition::fromArray(['valueType' => 'test_value_type']),
            '66',
        );

        $collection = $this->collectionService->loadCollection(Uuid::fromString('08937ca0-18f4-5806-84df-8c132c36cabe'));

        $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1,
        );
    }

    final public function testAddItemThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            ItemDefinition::fromArray(['valueType' => 'test_value_type']),
            '66',
        );

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $this->collectionService->addItem($collection, $itemCreateStruct, 9999);
    }

    final public function testUpdateItem(): void
    {
        $itemUpdateStruct = $this->collectionService->newItemUpdateStruct();

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param2', 42);

        $itemUpdateStruct->setConfigStruct('key', $configStruct);

        $item = $this->collectionService->loadItemDraft(Uuid::fromString('8ae55a69-8633-51dd-9ff5-d820d040c1c1'));

        $updatedItem = $this->collectionService->updateItem($item, $itemUpdateStruct);

        self::assertTrue($updatedItem->isDraft);
        self::assertTrue($updatedItem->hasConfig('key'));

        $itemConfig = $updatedItem->getConfig('key');
        self::assertNull($itemConfig->getParameter('param1')->value);
        self::assertSame(42, $itemConfig->getParameter('param2')->value);
    }

    final public function testUpdateItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "item" has an invalid state. Only draft items can be updated.');

        $itemUpdateStruct = $this->collectionService->newItemUpdateStruct();
        $item = $this->collectionService->loadItem(Uuid::fromString('79b6f162-d801-57e0-8b2d-a4b568a74231'));

        $this->collectionService->updateItem($item, $itemUpdateStruct);
    }

    final public function testMoveItem(): void
    {
        $movedItem = $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(Uuid::fromString('8ae55a69-8633-51dd-9ff5-d820d040c1c1')),
            1,
        );

        self::assertTrue($movedItem->isDraft);
        self::assertSame(1, $movedItem->position);

        $secondItem = $this->collectionService->loadItemDraft(Uuid::fromString('21e5d25d-7f2e-5020-a423-4cca08a5a7c9'));
        self::assertSame(0, $secondItem->position);
    }

    final public function testMoveItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "item" has an invalid state. Only draft items can be moved.');

        $this->collectionService->moveItem(
            $this->collectionService->loadItem(Uuid::fromString('79b6f162-d801-57e0-8b2d-a4b568a74231')),
            1,
        );
    }

    final public function testMoveItemThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(Uuid::fromString('8ae55a69-8633-51dd-9ff5-d820d040c1c1')),
            9999,
        );
    }

    final public function testDeleteItem(): void
    {
        $item = $this->collectionService->loadItemDraft(Uuid::fromString('8ae55a69-8633-51dd-9ff5-d820d040c1c1'));
        $this->collectionService->deleteItem($item);

        try {
            $this->collectionService->loadItemDraft($item->id);
            self::fail('Item still exists after deleting.');
        } catch (NotFoundException) {
            // Do nothing
        }

        $secondItem = $this->collectionService->loadItemDraft(Uuid::fromString('21e5d25d-7f2e-5020-a423-4cca08a5a7c9'));
        self::assertSame(0, $secondItem->position);
    }

    final public function testDeleteItemThrowsBadStateExceptionWithNonDraftItem(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "item" has an invalid state. Only draft items can be deleted.');

        $item = $this->collectionService->loadItem(Uuid::fromString('79b6f162-d801-57e0-8b2d-a4b568a74231'));
        $this->collectionService->deleteItem($item);
    }

    final public function testDeleteItems(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));
        $collection = $this->collectionService->deleteItems($collection);

        self::assertCount(0, $collection->items);
    }

    final public function testDeleteItemsThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Only items in draft collections can be deleted.');

        $collection = $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'));
        $this->collectionService->deleteItems($collection);
    }

    final public function testUpdateQuery(): void
    {
        $query = $this->collectionService->loadQueryDraft(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'), ['en']);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('hr');

        $queryUpdateStruct->setParameterValue('param', 'new_value');
        $queryUpdateStruct->setParameterValue('param2', 'value');

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        self::assertTrue($updatedQuery->isDraft);
        self::assertSame('test_query_type', $updatedQuery->queryType->type);

        self::assertNull($updatedQuery->getParameter('param')->value);
        self::assertNull($updatedQuery->getParameter('param2')->value);

        $croQuery = $this->collectionService->loadQueryDraft(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'), ['hr']);

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        self::assertNull($croQuery->getParameter('param')->value);

        self::assertSame('value', $croQuery->getParameter('param2')->value);
    }

    final public function testUpdateQueryInMainLocale(): void
    {
        $query = $this->collectionService->loadQueryDraft(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'), ['en']);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('en');

        $queryUpdateStruct->setParameterValue('param', 'new_value');
        $queryUpdateStruct->setParameterValue('param2', 'value');

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        self::assertTrue($updatedQuery->isDraft);
        self::assertSame('test_query_type', $updatedQuery->queryType->type);

        $croQuery = $this->collectionService->loadQueryDraft(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'), ['hr']);

        self::assertSame('new_value', $updatedQuery->getParameter('param')->value);
        self::assertSame('value', $updatedQuery->getParameter('param2')->value);

        // "param" parameter is untranslatable, meaning it keeps the value from main locale
        self::assertSame('new_value', $croQuery->getParameter('param')->value);

        self::assertNull($croQuery->getParameter('param2')->value);
    }

    final public function testUpdateQueryThrowsBadStateExceptionWithNonDraftQuery(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "query" has an invalid state. Only draft queries can be updated.');

        $query = $this->collectionService->loadQuery(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'));

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('en');
        $queryUpdateStruct->setParameterValue('param', 'value');
        $queryUpdateStruct->setParameterValue('param2', 'new_value');

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    final public function testUpdateQueryThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "query" has an invalid state. Query does not have the specified translation.');

        $query = $this->collectionService->loadQueryDraft(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66'));

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct('de');
        $queryUpdateStruct->setParameterValue('param', 'value');
        $queryUpdateStruct->setParameterValue('param2', 'new_value');

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    final public function testAddSlot(): void
    {
        $slotCreateStruct = $this->collectionService->newSlotCreateStruct();
        $slotCreateStruct->viewType = 'my_view_type';

        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));

        $createdSlot = $this->collectionService->addSlot(
            $collection,
            $slotCreateStruct,
            1,
        );

        self::assertTrue($createdSlot->isDraft);
        self::assertSame(1, $createdSlot->position);
        self::assertSame('my_view_type', $createdSlot->viewType);
    }

    final public function testAddSlotThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Slots can only be added to draft collections.');

        $slotCreateStruct = $this->collectionService->newSlotCreateStruct();

        $collection = $this->collectionService->loadCollection(Uuid::fromString('08937ca0-18f4-5806-84df-8c132c36cabe'));

        $this->collectionService->addSlot(
            $collection,
            $slotCreateStruct,
            1,
        );
    }

    final public function testUpdateSlot(): void
    {
        $slotUpdateStruct = $this->collectionService->newSlotUpdateStruct();
        $slotUpdateStruct->viewType = 'my_view_type';

        $slot = $this->collectionService->loadSlotDraft(Uuid::fromString('de3a0641-c67f-48e0-96e7-7c83b6735265'));

        $updatedSlot = $this->collectionService->updateSlot($slot, $slotUpdateStruct);

        self::assertTrue($updatedSlot->isDraft);
        self::assertSame('my_view_type', $updatedSlot->viewType);
    }

    final public function testUpdateSlotThrowsBadStateExceptionWithNonDraftSlot(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "slot" has an invalid state. Only draft slots can be updated.');

        $slotUpdateStruct = $this->collectionService->newSlotUpdateStruct();
        $slot = $this->collectionService->loadSlot(Uuid::fromString('c63c9523-e579-4dc9-b1d2-f9d12470a014'));

        $this->collectionService->updateSlot($slot, $slotUpdateStruct);
    }

    final public function testDeleteSlot(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find slot with identifier "de3a0641-c67f-48e0-96e7-7c83b6735265"');

        $slot = $this->collectionService->loadSlotDraft(Uuid::fromString('de3a0641-c67f-48e0-96e7-7c83b6735265'));
        $this->collectionService->deleteSlot($slot);

        $this->collectionService->loadSlotDraft(Uuid::fromString('de3a0641-c67f-48e0-96e7-7c83b6735265'));
    }

    final public function testDeleteSlotThrowsBadStateExceptionWithNonDraftSlot(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "slot" has an invalid state. Only draft slots can be deleted.');

        $slot = $this->collectionService->loadSlot(Uuid::fromString('c63c9523-e579-4dc9-b1d2-f9d12470a014'));
        $this->collectionService->deleteSlot($slot);
    }

    final public function testDeleteSlots(): void
    {
        $collection = $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'));
        $collection = $this->collectionService->deleteSlots($collection);

        self::assertCount(0, $collection->slots);
    }

    final public function testDeleteSlotsThrowsBadStateExceptionWithNonDraftCollection(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "collection" has an invalid state. Only slots in draft collections can be deleted.');

        $collection = $this->collectionService->loadCollection(Uuid::fromString('45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a'));
        $this->collectionService->deleteSlots($collection);
    }

    final public function testNewCollectionCreateStruct(): void
    {
        $queryCreateStruct = new QueryCreateStruct(new QueryType('test_query_type'));
        $struct = $this->collectionService->newCollectionCreateStruct($queryCreateStruct);

        self::assertSame(
            [
                'limit' => null,
                'offset' => 0,
                'queryCreateStruct' => $queryCreateStruct,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewCollectionUpdateStruct(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct();

        self::assertSame(
            [
                'limit' => null,
                'offset' => null,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewCollectionUpdateStructWithCollection(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89')),
        );

        self::assertSame(
            [
                'limit' => 2,
                'offset' => 4,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewCollectionUpdateStructWithUnlimitedCollection(): void
    {
        $struct = $this->collectionService->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e')),
        );

        self::assertSame(
            [
                'limit' => 0,
                'offset' => 0,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewItemCreateStruct(): void
    {
        $itemDefinition = new ItemDefinition();
        $struct = $this->collectionService->newItemCreateStruct($itemDefinition, '42');

        self::assertSame(
            [
                'configStructs' => [],
                'definition' => $itemDefinition,
                'value' => '42',
                'viewType' => null,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewItemUpdateStruct(): void
    {
        $struct = $this->collectionService->newItemUpdateStruct();

        self::assertSame(
            [
                'configStructs' => [],
                'viewType' => null,
            ],
            $this->exportObject($struct, true),
        );
    }

    final public function testNewItemUpdateStructFromItem(): void
    {
        $item = $this->collectionService->loadItemDraft(Uuid::fromString('8ae55a69-8633-51dd-9ff5-d820d040c1c1'));
        $struct = $this->collectionService->newItemUpdateStruct($item);

        self::assertArrayHasKey('key', $struct->configStructs);

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
                'viewType' => 'overlay',
            ],
            $this->exportObject($struct, true),
        );
    }

    final public function testNewItemUpdateStructFromItemWithNoViewType(): void
    {
        $item = $this->collectionService->loadItemDraft(Uuid::fromString('21e5d25d-7f2e-5020-a423-4cca08a5a7c9'));
        $struct = $this->collectionService->newItemUpdateStruct($item);

        self::assertArrayHasKey('key', $struct->configStructs);

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
                'viewType' => '',
            ],
            $this->exportObject($struct, true),
        );
    }

    final public function testNewQueryCreateStruct(): void
    {
        $queryType = new QueryType('test_query_type');

        $struct = $this->collectionService->newQueryCreateStruct($queryType);

        self::assertSame(
            [
                'parameterValues' => [
                    'param' => 'value',
                    'param2' => null,
                ],
                'queryType' => $queryType,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewQueryUpdateStruct(): void
    {
        $struct = $this->collectionService->newQueryUpdateStruct('en');

        self::assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [],
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewQueryUpdateStructFromQuery(): void
    {
        $query = $this->collectionService->loadQueryDraft(Uuid::fromString('6d60fcbc-ae38-57c2-af72-e462a3e5c9f2'));
        $struct = $this->collectionService->newQueryUpdateStruct('en', $query);

        self::assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [
                    'param' => null,
                    'param2' => null,
                ],
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewSlotCreateStruct(): void
    {
        $struct = $this->collectionService->newSlotCreateStruct();

        self::assertSame(
            [
                'viewType' => null,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewSlotUpdateStruct(): void
    {
        $struct = $this->collectionService->newSlotUpdateStruct();

        self::assertSame(
            [
                'viewType' => null,
            ],
            $this->exportObject($struct, true),
        );
    }

    final public function testNewSlotUpdateStructFromSlot(): void
    {
        $slot = $this->collectionService->loadSlotDraft(Uuid::fromString('de3a0641-c67f-48e0-96e7-7c83b6735265'));
        $struct = $this->collectionService->newSlotUpdateStruct($slot);

        self::assertSame(
            [
                'viewType' => 'standard',
            ],
            $this->exportObject($struct, true),
        );
    }
}
