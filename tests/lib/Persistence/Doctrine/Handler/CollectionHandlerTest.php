<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\QueryTranslationUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Slot;
use Netgen\Layouts\Persistence\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\SlotUpdateStruct;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use PHPUnit\Framework\TestCase;

final class CollectionHandlerTest extends TestCase
{
    use ExportObjectTrait;
    use TestCaseTrait;
    use UuidGeneratorTrait;

    private CollectionHandlerInterface $collectionHandler;

    private BlockHandlerInterface $blockHandler;

    protected function setUp(): void
    {
        $this->createDatabase();

        $this->collectionHandler = $this->createCollectionHandler();
        $this->blockHandler = $this->createBlockHandler();
    }

    /**
     * Tears down the tests.
     */
    protected function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getCollectionWithBlockSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testLoadCollection(): void
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 1,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
            ],
            $this->exportObject($collection),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testLoadCollectionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "999"');

        $this->collectionHandler->loadCollection(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollections
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getCollectionSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadBlockCollectionsData
     */
    public function testLoadCollections(): void
    {
        $collections = $this->collectionHandler->loadCollections(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
        );

        self::assertSame(
            [
                'default' => [
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'blockId' => 31,
                    'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                    'id' => 1,
                    'isTranslatable' => true,
                    'limit' => null,
                    'mainLocale' => 'en',
                    'offset' => 0,
                    'status' => Value::STATUS_DRAFT,
                    'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                ],
                'featured' => [
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'blockId' => 31,
                    'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                    'id' => 3,
                    'isTranslatable' => true,
                    'limit' => 2,
                    'mainLocale' => 'en',
                    'offset' => 4,
                    'status' => Value::STATUS_DRAFT,
                    'uuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                ],
            ],
            $this->exportObjectList($collections),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionReference
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionReferencesData
     */
    public function testLoadCollectionReference(): void
    {
        $reference = $this->collectionHandler->loadCollectionReference(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'default',
        );

        self::assertSame(
            [
                'blockId' => 31,
                'blockStatus' => Value::STATUS_DRAFT,
                'collectionId' => 1,
                'collectionStatus' => Value::STATUS_DRAFT,
                'identifier' => 'default',
            ],
            $this->exportObject($reference),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionReference
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionReferencesData
     */
    public function testLoadCollectionReferenceThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection reference with identifier "non_existing"');

        $this->collectionHandler->loadCollectionReference(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'non_existing',
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionReferences
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionReferencesData
     */
    public function testLoadCollectionReferences(): void
    {
        $references = $this->collectionHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
        );

        self::assertContainsOnlyInstancesOf(CollectionReference::class, $references);

        self::assertSame(
            [
                [
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 1,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                ],
                [
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 3,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'featured',
                ],
            ],
            $this->exportObjectList($references),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     */
    public function testLoadItem(): void
    {
        $item = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => [],
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '8ae55a69-8633-51dd-9ff5-d820d040c1c1',
                'value' => '72',
                'valueType' => 'my_value_type',
                'viewType' => 'overlay',
            ],
            $this->exportObject($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find item with identifier "999"');

        $this->collectionHandler->loadItem(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadItemWithPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemWithPositionData
     */
    public function testLoadItemWithPosition(): void
    {
        $item = $this->collectionHandler->loadItemWithPosition(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            0,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => [],
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '8ae55a69-8633-51dd-9ff5-d820d040c1c1',
                'value' => '72',
                'valueType' => 'my_value_type',
                'viewType' => 'overlay',
            ],
            $this->exportObject($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadItemWithPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemWithPositionData
     */
    public function testLoadItemWithPositionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find item in collection with ID "1" at position 9999');

        $this->collectionHandler->loadItemWithPosition(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItems(): void
    {
        $items = $this->collectionHandler->loadCollectionItems(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
        );

        self::assertNotEmpty($items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getQuerySelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     */
    public function testLoadQuery(): void
    {
        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'id' => 1,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '86c5af5d-bcb3-5a93-aeed-754466d76878',
            ],
            $this->exportObject($query),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     */
    public function testLoadQueryThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "999"');

        $this->collectionHandler->loadQuery(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testLoadCollectionQuery(): void
    {
        $query = $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'id' => 1,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '86c5af5d-bcb3-5a93-aeed-754466d76878',
            ],
            $this->exportObject($query),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testLoadCollectionQueryThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query for collection with identifier "1"');

        $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadSlot
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getSlotSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadSlotData
     */
    public function testLoadSlot(): void
    {
        $slot = $this->collectionHandler->loadSlot(1, Value::STATUS_DRAFT);

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'de3a0641-c67f-48e0-96e7-7c83b6735265',
                'viewType' => 'standard',
            ],
            $this->exportObject($slot),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadSlot
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadSlotData
     */
    public function testLoadSlotThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find slot with identifier "999"');

        $this->collectionHandler->loadSlot(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionSlots
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionSlotsData
     */
    public function testLoadCollectionSlots(): void
    {
        $slots = $this->collectionHandler->loadCollectionSlots(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
        );

        self::assertNotEmpty($slots);
        self::assertContainsOnlyInstancesOf(Slot::class, $slots);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionExists(): void
    {
        self::assertTrue($this->collectionHandler->collectionExists(1, Value::STATUS_DRAFT));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExists(): void
    {
        self::assertFalse($this->collectionHandler->collectionExists(999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExistsInStatus(): void
    {
        self::assertFalse($this->collectionHandler->collectionExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     */
    public function testCreateCollection(): void
    {
        $block = $this->blockHandler->loadBlock(38, Value::STATUS_DRAFT);

        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->status = Value::STATUS_DRAFT;
        $collectionCreateStruct->offset = 5;
        $collectionCreateStruct->limit = 10;
        $collectionCreateStruct->mainLocale = 'en';
        $collectionCreateStruct->isTranslatable = true;
        $collectionCreateStruct->alwaysAvailable = true;

        $createdCollection = $this->withUuids(
            fn (): Collection => $this->collectionHandler->createCollection($collectionCreateStruct, $block, 'default'),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'blockId' => 38,
                'blockUuid' => 'a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6',
                'id' => 7,
                'isTranslatable' => true,
                'limit' => 10,
                'mainLocale' => 'en',
                'offset' => 5,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            $this->exportObject($createdCollection),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     */
    public function testCreateCollectionThrowsBadStateExceptionWithBlockInDifferentStatus(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Collections can only be created in blocks with the same status.');

        $block = $this->blockHandler->loadBlock(38, Value::STATUS_DRAFT);

        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->status = Value::STATUS_PUBLISHED;
        $collectionCreateStruct->offset = 5;
        $collectionCreateStruct->limit = 10;
        $collectionCreateStruct->mainLocale = 'en';
        $collectionCreateStruct->isTranslatable = true;
        $collectionCreateStruct->alwaysAvailable = true;

        $this->collectionHandler->createCollection($collectionCreateStruct, $block, 'default');
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQueryTranslation
     */
    public function testCreateCollectionTranslation(): void
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'en',
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 2,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
            ],
            $this->exportObject($collection),
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['de', 'en', 'hr'],
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'id' => 1,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'de' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '86c5af5d-bcb3-5a93-aeed-754466d76878',
            ],
            $this->exportObject($query),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationWithNonMainSourceLocale(): void
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'hr',
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 2,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
            ],
            $this->exportObject($collection),
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['de', 'en', 'hr'],
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'id' => 1,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'de' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '86c5af5d-bcb3-5a93-aeed-754466d76878',
            ],
            $this->exportObject($query),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationForCollectionWithNoQuery(): void
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            'de',
            'en',
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 1,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
            ],
            $this->exportObject($collection),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Collection already has the provided locale.');

        $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'en',
            'hr',
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationThrowsBadStateExceptionWithNonExistingSourceLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Collection does not have the provided source locale.');

        $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'fr',
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionReference
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionReference
     */
    public function testCreateCollectionReference(): void
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);

        $reference = $this->collectionHandler->createCollectionReference(
            $collection,
            $block,
            'new',
        );

        self::assertSame(
            [
                'blockId' => $block->id,
                'blockStatus' => $block->status,
                'collectionId' => $collection->id,
                'collectionStatus' => $collection->status,
                'identifier' => 'new',
            ],
            $this->exportObject($reference),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testSetMainTranslation(): void
    {
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $collection = $this->collectionHandler->setMainTranslation($collection, 'hr');

        self::assertSame('hr', $collection->mainLocale);

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);
        self::assertSame('hr', $query->mainLocale);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testSetMainTranslationForCollectionWithNoQuery(): void
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
        $collection = $this->collectionHandler->setMainTranslation($collection, 'hr');

        self::assertSame('hr', $collection->mainLocale);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "mainLocale" has an invalid state. Collection does not have the provided locale.');

        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $this->collectionHandler->setMainTranslation($collection, 'de');
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollection(): void
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->offset = 5;
        $collectionUpdateStruct->limit = 10;
        $collectionUpdateStruct->isTranslatable = false;
        $collectionUpdateStruct->alwaysAvailable = false;

        $updatedCollection = $this->collectionHandler->updateCollection(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $collectionUpdateStruct,
        );

        self::assertSame(
            [
                'alwaysAvailable' => false,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 1,
                'isTranslatable' => false,
                'limit' => 10,
                'mainLocale' => 'en',
                'offset' => 5,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
            ],
            $this->exportObject($updatedCollection),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollectionWithNoLimit(): void
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->offset = 5;
        $collectionUpdateStruct->limit = 0;

        $updatedCollection = $this->collectionHandler->updateCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $collectionUpdateStruct,
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 3,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 5,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
            ],
            $this->exportObject($updatedCollection),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollectionWithDefaultValues(): void
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        $updatedCollection = $this->collectionHandler->updateCollection(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $collectionUpdateStruct,
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 1,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
            ],
            $this->exportObject($updatedCollection),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testCopyCollection(): void
    {
        $block = $this->blockHandler->loadBlock(34, Value::STATUS_PUBLISHED);

        $copiedCollection = $this->withUuids(
            fn (): Collection => $this->collectionHandler->copyCollection(
                $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
                $block,
                'default',
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
            ],
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 34,
                'blockUuid' => '42446cc9-24c3-573c-9022-6b3a764727b5',
                'id' => 7,
                'isTranslatable' => true,
                'limit' => 2,
                'mainLocale' => 'en',
                'offset' => 4,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            $this->exportObject($copiedCollection),
        );

        self::assertSame(
            [
                [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'config' => [],
                    'id' => 13,
                    'position' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'uuid' => '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
                [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'config' => [],
                    'id' => 14,
                    'position' => 3,
                    'status' => Value::STATUS_PUBLISHED,
                    'uuid' => '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
                [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'config' => [],
                    'id' => 15,
                    'position' => 5,
                    'status' => Value::STATUS_PUBLISHED,
                    'uuid' => '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection),
            ),
        );

        $query = $this->collectionHandler->loadCollectionQuery($copiedCollection);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'collectionId' => $copiedCollection->id,
                'collectionUuid' => $copiedCollection->uuid,
                'id' => 5,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
            ],
            $this->exportObject($query),
        );

        self::assertSame(
            [
                3 => [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'id' => 7,
                    'position' => 3,
                    'status' => Value::STATUS_PUBLISHED,
                    'uuid' => '8634280c-f498-416e-b4a7-0b0bd0869c85',
                    'viewType' => 'standard',
                ],
                5 => [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'id' => 8,
                    'position' => 5,
                    'status' => Value::STATUS_PUBLISHED,
                    'uuid' => '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                    'viewType' => 'overlay',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionSlots($copiedCollection),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testCopyCollectionWithoutQuery(): void
    {
        $block = $this->blockHandler->loadBlock(34, Value::STATUS_DRAFT);

        $copiedCollection = $this->withUuids(
            fn (): Collection => $this->collectionHandler->copyCollection(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $block,
                'default',
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
            ],
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 34,
                'blockUuid' => '42446cc9-24c3-573c-9022-6b3a764727b5',
                'id' => 7,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            $this->exportObject($copiedCollection),
        );

        self::assertSame(
            [
                [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'config' => [],
                    'id' => 13,
                    'position' => 0,
                    'status' => Value::STATUS_DRAFT,
                    'uuid' => '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'viewType' => 'overlay',
                ],
                [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'config' => [],
                    'id' => 14,
                    'position' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'uuid' => '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
                [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'config' => [],
                    'id' => 15,
                    'position' => 2,
                    'status' => Value::STATUS_DRAFT,
                    'uuid' => '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'viewType' => 'overlay',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection),
            ),
        );

        self::assertSame(
            [
                0 => [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'id' => 7,
                    'position' => 0,
                    'status' => Value::STATUS_DRAFT,
                    'uuid' => '8634280c-f498-416e-b4a7-0b0bd0869c85',
                    'viewType' => 'standard',
                ],
                2 => [
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'id' => 8,
                    'position' => 2,
                    'status' => Value::STATUS_DRAFT,
                    'uuid' => '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                    'viewType' => 'overlay',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionSlots($copiedCollection),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     */
    public function testCopyCollectionThrowsBadStateExceptionWithBlockInDifferentStatus(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Collections can only be copied to blocks with the same status.');

        $block = $this->blockHandler->loadBlock(34, Value::STATUS_DRAFT);

        $this->collectionHandler->copyCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $block,
            'default',
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testCreateCollectionStatus(): void
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            Value::STATUS_ARCHIVED,
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 3,
                'isTranslatable' => true,
                'limit' => 2,
                'mainLocale' => 'en',
                'offset' => 4,
                'status' => Value::STATUS_ARCHIVED,
                'uuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
            ],
            $this->exportObject($copiedCollection),
        );

        self::assertSame(
            [
                [
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'config' => [],
                    'id' => 7,
                    'position' => 2,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => '89c214a3-204f-5352-85d7-8852b26ab6b0',
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
                [
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'config' => [],
                    'id' => 8,
                    'position' => 3,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => 'f6eb491a-e273-5ab0-85a3-f5765195b2dd',
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
                [
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'config' => [],
                    'id' => 9,
                    'position' => 5,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => '9701e116-51f4-5ff6-b9b5-5660cb2ab21d',
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection),
            ),
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'id' => 2,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_ARCHIVED,
                'type' => 'my_query_type',
                'uuid' => '0303abc4-c894-59b5-ba95-5cf330b99c66',
            ],
            $this->exportObject(
                $this->collectionHandler->loadCollectionQuery($copiedCollection),
            ),
        );

        self::assertSame(
            [
                3 => [
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'id' => 5,
                    'position' => 3,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => 'd0c55af8-5a45-4221-84e6-c7e4b975db0e',
                    'viewType' => 'standard',
                ],
                5 => [
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'id' => 6,
                    'position' => 5,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => 'f520bcc4-e977-4c51-85cb-f68c79884e81',
                    'viewType' => 'overlay',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionSlots($copiedCollection),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testCreateCollectionStatusWithoutQuery(): void
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            Value::STATUS_ARCHIVED,
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 1,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_ARCHIVED,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
            ],
            $this->exportObject($copiedCollection),
        );

        self::assertSame(
            [
                [
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'config' => [],
                    'id' => 1,
                    'position' => 0,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => '8ae55a69-8633-51dd-9ff5-d820d040c1c1',
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'viewType' => 'overlay',
                ],
                [
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'config' => [],
                    'id' => 2,
                    'position' => 1,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => '21e5d25d-7f2e-5020-a423-4cca08a5a7c9',
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'viewType' => null,
                ],
                [
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'config' => [],
                    'id' => 3,
                    'position' => 2,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => '02e890ee-6d30-513a-9d13-a3897bb6c3ab',
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'viewType' => 'overlay',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection),
            ),
        );

        self::assertSame(
            [
                0 => [
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'id' => 1,
                    'position' => 0,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => 'de3a0641-c67f-48e0-96e7-7c83b6735265',
                    'viewType' => 'standard',
                ],
                2 => [
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'id' => 2,
                    'position' => 2,
                    'status' => Value::STATUS_ARCHIVED,
                    'uuid' => 'ee232f5b-478c-4513-a4b4-19e7e8b03aab',
                    'viewType' => 'overlay',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionSlots($copiedCollection),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollection(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "3"');

        $this->collectionHandler->deleteCollection(3);

        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionWithoutQuery(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "1"');

        $this->collectionHandler->deleteCollection(1);

        $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionInOneStatus(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "3"');

        $this->collectionHandler->deleteCollection(3, Value::STATUS_DRAFT);

        // First, verify that NOT all collection statuses are deleted
        try {
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the collection in draft status deleted other/all statuses.');
        }

        $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQueryTranslations
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionTranslation(): void
    {
        $collection = $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'hr',
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 2,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
            ],
            $this->exportObject($collection),
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'id' => 1,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '86c5af5d-bcb3-5a93-aeed-754466d76878',
            ],
            $this->exportObject($query),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     */
    public function testDeleteCollectionTranslationForCollectionWithNoQuery(): void
    {
        $collection = $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            'hr',
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'blockId' => 31,
                'blockUuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'id' => 1,
                'isTranslatable' => true,
                'limit' => null,
                'mainLocale' => 'en',
                'offset' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
            ],
            $this->exportObject($collection),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     */
    public function testDeleteCollectionTranslationWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Collection does not have the provided locale.');

        $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     */
    public function testDeleteCollectionTranslationWithMainLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Main translation cannot be removed from the collection.');

        $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'en',
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItem(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = 1;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->viewType = 'my_view_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->withUuids(
            fn (): Item => $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $itemCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => ['config' => ['value' => 42]],
                'id' => 13,
                'position' => 1,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => '42',
                'valueType' => 'my_value_type',
                'viewType' => 'my_view_type',
            ],
            $this->exportObject($item),
        );

        $secondItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        self::assertSame(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemToDynamicCollection(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = 2;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->viewType = 'my_view_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->withUuids(
            fn (): Item => $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
                $itemCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'config' => ['config' => ['value' => 42]],
                'id' => 13,
                'position' => 2,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => '42',
                'valueType' => 'my_value_type',
                'viewType' => 'my_view_type',
            ],
            $this->exportObject($item),
        );

        $secondItem = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
        self::assertSame(3, $secondItem->position);

        $thirdItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        self::assertSame(4, $thirdItem->position);

        $fourthItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        self::assertSame(5, $fourthItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemToDynamicCollectionWithNoItemInPosition(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = 4;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->viewType = 'my_view_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->withUuids(
            fn (): Item => $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
                $itemCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'config' => ['config' => ['value' => 42]],
                'id' => 13,
                'position' => 4,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => '42',
                'valueType' => 'my_value_type',
                'viewType' => 'my_view_type',
            ],
            $this->exportObject($item),
        );

        $secondItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        self::assertSame(5, $secondItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemWithNoPosition(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = null;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];
        $itemCreateStruct->viewType = null;

        $item = $this->withUuids(
            fn (): Item => $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $itemCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => ['config' => ['value' => 42]],
                'id' => 13,
                'position' => 3,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => '42',
                'valueType' => 'my_value_type',
                'viewType' => null,
            ],
            $this->exportObject($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemToDynamicCollectionWithoutPositionThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('When adding items to dynamic collections, position is mandatory.');

        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = null;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = [];
        $itemCreateStruct->viewType = null;

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $itemCreateStruct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position cannot be negative.');

        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = -1;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = [];

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = 9999;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = [];

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testUpdateItem(): void
    {
        $itemUpdateStruct = new ItemUpdateStruct();
        $itemUpdateStruct->viewType = 'new_view_type';
        $itemUpdateStruct->config = [
            'new_config' => [
                'val' => 24,
            ],
        ];

        $item = $this->collectionHandler->updateItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            $itemUpdateStruct,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => ['new_config' => ['val' => 24]],
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '8ae55a69-8633-51dd-9ff5-d820d040c1c1',
                'value' => '72',
                'valueType' => 'my_value_type',
                'viewType' => 'new_view_type',
            ],
            $this->exportObject($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testUpdateItemWithResettingViewType(): void
    {
        $itemUpdateStruct = new ItemUpdateStruct();
        $itemUpdateStruct->viewType = '';

        $item = $this->collectionHandler->updateItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            $itemUpdateStruct,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => [],
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '8ae55a69-8633-51dd-9ff5-d820d040c1c1',
                'value' => '72',
                'valueType' => 'my_value_type',
                'viewType' => null,
            ],
            $this->exportObject($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItem(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(12, Value::STATUS_DRAFT),
            2,
        );

        self::assertSame(
            [
                'collectionId' => 4,
                'collectionUuid' => '08937ca0-18f4-5806-84df-8c132c36cabe',
                'config' => [],
                'id' => 12,
                'position' => 2,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '3562a253-72d1-54d1-8b31-ef1b55409cb5',
                'value' => '74',
                'valueType' => 'my_value_type',
                'viewType' => null,
            ],
            $this->exportObject($movedItem),
        );

        $firstItem = $this->collectionHandler->loadItem(10, Value::STATUS_DRAFT);
        self::assertSame(3, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(11, Value::STATUS_DRAFT);
        self::assertSame(4, $secondItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemWithSwitchingPositions(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            1,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => [],
                'id' => 1,
                'position' => 1,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '8ae55a69-8633-51dd-9ff5-d820d040c1c1',
                'value' => '72',
                'valueType' => 'my_value_type',
                'viewType' => 'overlay',
            ],
            $this->exportObject($movedItem),
        );

        $firstItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        self::assertSame(0, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        self::assertSame(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToSamePosition(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            0,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => [],
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '8ae55a69-8633-51dd-9ff5-d820d040c1c1',
                'value' => '72',
                'valueType' => 'my_value_type',
                'viewType' => 'overlay',
            ],
            $this->exportObject($movedItem),
        );

        $firstItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        self::assertSame(1, $firstItem->position);

        $firstItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        self::assertSame(2, $firstItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPosition(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT),
            0,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => [],
                'id' => 2,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '21e5d25d-7f2e-5020-a423-4cca08a5a7c9',
                'value' => '73',
                'valueType' => 'my_value_type',
                'viewType' => null,
            ],
            $this->exportObject($movedItem),
        );

        $firstItem = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);
        self::assertSame(1, $firstItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPositionWithSwitchingPositions(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT),
            1,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'config' => [],
                'id' => 3,
                'position' => 1,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '02e890ee-6d30-513a-9d13-a3897bb6c3ab',
                'value' => '74',
                'valueType' => 'my_value_type',
                'viewType' => 'overlay',
            ],
            $this->exportObject($movedItem),
        );

        $firstItem = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);
        self::assertSame(0, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        self::assertSame(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemInDynamicCollection(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT),
            4,
        );

        self::assertSame(
            [
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'config' => [],
                'id' => 7,
                'position' => 4,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '89c214a3-204f-5352-85d7-8852b26ab6b0',
                'value' => '72',
                'valueType' => 'my_value_type',
                'viewType' => null,
            ],
            $this->exportObject($movedItem),
        );

        $secondItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        self::assertSame(3, $secondItem->position);

        $thirdItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        self::assertSame(5, $thirdItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPositionInDynamicCollection(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT),
            2,
        );

        self::assertSame(
            [
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'config' => [],
                'id' => 8,
                'position' => 2,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f6eb491a-e273-5ab0-85a3-f5765195b2dd',
                'value' => '73',
                'valueType' => 'my_value_type',
                'viewType' => null,
            ],
            $this->exportObject($movedItem),
        );

        $firstItem = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
        self::assertSame(3, $firstItem->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     */
    public function testMoveItemThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position cannot be negative.');

        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            -1,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     */
    public function testMoveItemThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testSwitchItemPositions(): void
    {
        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);

        $updatedItem1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $updatedItem2 = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);

        self::assertSame($item2->position, $updatedItem1->position);
        self::assertSame($item1->position, $updatedItem2->position);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     */
    public function testSwitchItemPositionsThrowsBadStateExceptionWithSameItem(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('First and second items are the same.');

        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     */
    public function testSwitchItemPositionsThrowsBadStateExceptionWithItemsFromDifferentCollections(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Positions can be switched only for items within the same collection.');

        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     */
    public function testDeleteItem(): void
    {
        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT),
        );

        $secondItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        self::assertSame(1, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     */
    public function testDeleteItemFromDynamicCollection(): void
    {
        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT),
        );

        $secondItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        self::assertSame(3, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteItems
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     */
    public function testDeleteItems(): void
    {
        $collection = $this->collectionHandler->deleteItems(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
        );

        self::assertCount(0, $this->collectionHandler->loadCollectionItems($collection));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     */
    public function testCreateQuery(): void
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);

        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->type = 'my_query_type';
        $queryCreateStruct->parameters = [
            'param' => 'value',
        ];

        $createdQuery = $this->withUuids(
            fn (): Query => $this->collectionHandler->createQuery(
                $collection,
                $queryCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'id' => 5,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'param' => 'value',
                    ],
                    'hr' => [
                        'param' => 'value',
                    ],
                ],
                'status' => Value::STATUS_DRAFT,
                'type' => 'my_query_type',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            $this->exportObject($createdQuery),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::createQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     */
    public function testCreateQueryThrowsBadStateExceptionWithExistingQuery(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Provided collection already has a query.');

        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->type = 'my_query_type';
        $queryCreateStruct->parameters = [
            'param' => 'value',
        ];

        $this->collectionHandler->createQuery(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $queryCreateStruct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     */
    public function testUpdateQueryTranslation(): void
    {
        $translationUpdateStruct = new QueryTranslationUpdateStruct();

        $translationUpdateStruct->parameters = [
            'parent_location_id' => 999,
            'some_param' => 'Some value',
        ];

        $updatedQuery = $this->collectionHandler->updateQueryTranslation(
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
            'en',
            $translationUpdateStruct,
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'id' => 1,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 999,
                        'some_param' => 'Some value',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '86c5af5d-bcb3-5a93-aeed-754466d76878',
            ],
            $this->exportObject($updatedQuery),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     */
    public function testUpdateQueryTranslationWithDefaultValues(): void
    {
        $translationUpdateStruct = new QueryTranslationUpdateStruct();

        $updatedQuery = $this->collectionHandler->updateQueryTranslation(
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
            'en',
            $translationUpdateStruct,
        );

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr'],
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'id' => 1,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                    'hr' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'my_query_type',
                'uuid' => '86c5af5d-bcb3-5a93-aeed-754466d76878',
            ],
            $this->exportObject($updatedQuery),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     */
    public function testUpdateQueryTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Query does not have the provided locale.');

        $this->collectionHandler->updateQueryTranslation(
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
            'de',
            new QueryTranslationUpdateStruct(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionQuery(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "2"');

        $this->collectionHandler->deleteCollectionQuery(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
        );

        // Query with ID 2 was in the collection with ID 3
        $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionQueryWithNoQuery(): void
    {
        $this->collectionHandler->deleteCollectionQuery(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
        );

        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::slotWithPositionExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::slotWithPositionExists
     */
    public function testSlotWithPositionExists(): void
    {
        self::assertTrue(
            $this->collectionHandler->slotWithPositionExists(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                0,
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::slotWithPositionExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::slotWithPositionExists
     */
    public function testSlotWithPositionNotExists(): void
    {
        self::assertFalse(
            $this->collectionHandler->slotWithPositionExists(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                999,
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addSlot
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addSlot
     */
    public function testAddSlot(): void
    {
        $slotCreateStruct = new SlotCreateStruct();
        $slotCreateStruct->position = 1;
        $slotCreateStruct->viewType = 'my_view_type';

        $slot = $this->withUuids(
            fn (): Slot => $this->collectionHandler->addSlot(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $slotCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'id' => 7,
                'position' => 1,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'my_view_type',
            ],
            $this->exportObject($slot),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::addSlot
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addSlot
     */
    public function testAddSlotThrowsBadStateExceptionWithExistingPosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Slot with provided position already exists in the collection with ID 1');

        $slotCreateStruct = new SlotCreateStruct();
        $slotCreateStruct->position = 0;
        $slotCreateStruct->viewType = 'my_view_type';

        $this->collectionHandler->addSlot(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $slotCreateStruct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateSlot
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateSlot
     */
    public function testUpdateSlot(): void
    {
        $slotUpdateStruct = new SlotUpdateStruct();
        $slotUpdateStruct->viewType = 'new_view_type';

        $slot = $this->collectionHandler->updateSlot(
            $this->collectionHandler->loadSlot(1, Value::STATUS_DRAFT),
            $slotUpdateStruct,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'de3a0641-c67f-48e0-96e7-7c83b6735265',
                'viewType' => 'new_view_type',
            ],
            $this->exportObject($slot),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateSlot
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateSlot
     */
    public function testUpdateSlotWithResettingViewType(): void
    {
        $slotUpdateStruct = new SlotUpdateStruct();
        $slotUpdateStruct->viewType = '';

        $slot = $this->collectionHandler->updateSlot(
            $this->collectionHandler->loadSlot(1, Value::STATUS_DRAFT),
            $slotUpdateStruct,
        );

        self::assertSame(
            [
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'id' => 1,
                'position' => 0,
                'status' => Value::STATUS_DRAFT,
                'uuid' => 'de3a0641-c67f-48e0-96e7-7c83b6735265',
                'viewType' => null,
            ],
            $this->exportObject($slot),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteSlot
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteSlot
     */
    public function testDeleteSlot(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find slot with identifier "1"');

        $this->collectionHandler->deleteSlot(
            $this->collectionHandler->loadSlot(1, Value::STATUS_DRAFT),
        );

        $this->collectionHandler->loadSlot(1, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::deleteSlots
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionSlots
     */
    public function testDeleteSlots(): void
    {
        $collection = $this->collectionHandler->deleteSlots(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
        );

        self::assertCount(0, $this->collectionHandler->loadCollectionSlots($collection));
    }
}
