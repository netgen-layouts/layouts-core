<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\QueryTranslationUpdateStruct;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use PHPUnit\Framework\TestCase;

final class CollectionHandlerTest extends TestCase
{
    use TestCaseTrait;
    use ExportObjectTrait;
    use UuidGeneratorTrait;

    /**
     * @var \Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    public function setUp(): void
    {
        $this->createDatabase();

        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getCollectionSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testLoadCollection(): void
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);

        self::assertSame(
            [
                'id' => 1,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($collection)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testLoadCollectionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "999999"');

        $this->collectionHandler->loadCollection(999999, Value::STATUS_PUBLISHED);
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
                'id' => 1,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 0,
                'value' => '72',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($item)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find item with identifier "999999"');

        $this->collectionHandler->loadItem(999999, Value::STATUS_PUBLISHED);
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
            0
        );

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 0,
                'value' => '72',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($item)
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
            9999
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItems(): void
    {
        $items = $this->collectionHandler->loadCollectionItems(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
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
                'id' => 1,
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($query)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     */
    public function testLoadQueryThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "999999"');

        $this->collectionHandler->loadQuery(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testLoadCollectionQuery(): void
    {
        $query = $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED)
        );

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($query)
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
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );
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
        self::assertFalse($this->collectionHandler->collectionExists(999999, Value::STATUS_PUBLISHED));
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     */
    public function testCreateCollection(): void
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->status = Value::STATUS_DRAFT;
        $collectionCreateStruct->offset = 5;
        $collectionCreateStruct->limit = 10;
        $collectionCreateStruct->mainLocale = 'en';
        $collectionCreateStruct->isTranslatable = true;
        $collectionCreateStruct->alwaysAvailable = true;

        $createdCollection = $this->withUuids(
            function () use ($collectionCreateStruct): Collection {
                return $this->collectionHandler->createCollection($collectionCreateStruct);
            },
            ['f06f245a-f951-52c8-bfa3-84c80154eadc']
        );

        self::assertSame(
            [
                'id' => 7,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'offset' => 5,
                'limit' => 10,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($createdCollection)
        );
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
            'en'
        );

        self::assertSame(
            [
                'id' => 2,
                'uuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($collection)
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['de', 'en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($query)
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
            'hr'
        );

        self::assertSame(
            [
                'id' => 2,
                'uuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($collection)
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['de', 'en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($query)
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
            'en'
        );

        self::assertSame(
            [
                'id' => 1,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($collection)
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
            'hr'
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
            'fr'
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
            $collectionUpdateStruct
        );

        self::assertSame(
            [
                'id' => 1,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'offset' => 5,
                'limit' => 10,
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => false,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedCollection)
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
            $collectionUpdateStruct
        );

        self::assertSame(
            [
                'id' => 3,
                'uuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'offset' => 5,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedCollection)
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
            $collectionUpdateStruct
        );

        self::assertSame(
            [
                'id' => 1,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedCollection)
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
        $copiedCollection = $this->withUuids(
            function (): Collection {
                return $this->collectionHandler->copyCollection(
                    $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED)
                );
            },
            ['f06f245a-f951-52c8-bfa3-84c80154eadc']
        );

        self::assertSame(
            [
                'id' => 7,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'offset' => 4,
                'limit' => 2,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($copiedCollection)
        );

        self::assertSame(
            [
                [
                    'id' => 13,
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'position' => 2,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 14,
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'position' => 3,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 15,
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'position' => 5,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
        );

        $query = $this->collectionHandler->loadCollectionQuery($copiedCollection);

        self::assertSame(
            [
                'id' => 5,
                'collectionId' => $copiedCollection->id,
                'collectionUuid' => $copiedCollection->uuid,
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($query)
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
        $copiedCollection = $this->withUuids(
            function (): Collection {
                return $this->collectionHandler->copyCollection(
                    $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
                );
            },
            ['f06f245a-f951-52c8-bfa3-84c80154eadc']
        );

        self::assertSame(
            [
                'id' => 7,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedCollection)
        );

        self::assertSame(
            [
                [
                    'id' => 13,
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'position' => 0,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_DRAFT,
                ],
                [
                    'id' => 14,
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'position' => 1,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_DRAFT,
                ],
                [
                    'id' => 15,
                    'collectionId' => $copiedCollection->id,
                    'collectionUuid' => $copiedCollection->uuid,
                    'position' => 2,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_DRAFT,
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
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
            Value::STATUS_ARCHIVED
        );

        self::assertSame(
            [
                'id' => 3,
                'uuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'offset' => 4,
                'limit' => 2,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_ARCHIVED,
            ],
            $this->exportObject($copiedCollection)
        );

        self::assertSame(
            [
                [
                    'id' => 7,
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'position' => 2,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_ARCHIVED,
                ],
                [
                    'id' => 8,
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'position' => 3,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_ARCHIVED,
                ],
                [
                    'id' => 9,
                    'collectionId' => 3,
                    'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                    'position' => 5,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_ARCHIVED,
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
        );

        self::assertSame(
            [
                'id' => 2,
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_ARCHIVED,
            ],
            $this->exportObject(
                $this->collectionHandler->loadCollectionQuery($copiedCollection)
            )
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
            Value::STATUS_ARCHIVED
        );

        self::assertSame(
            [
                'id' => 1,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_ARCHIVED,
            ],
            $this->exportObject($copiedCollection)
        );

        self::assertSame(
            [
                [
                    'id' => 1,
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'position' => 0,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_ARCHIVED,
                ],
                [
                    'id' => 2,
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'position' => 1,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_ARCHIVED,
                ],
                [
                    'id' => 3,
                    'collectionId' => 1,
                    'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                    'position' => 2,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'config' => [],
                    'status' => Value::STATUS_ARCHIVED,
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
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
            'hr'
        );

        self::assertSame(
            [
                'id' => 2,
                'uuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($collection)
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'type' => 'my_query_type',
                'parameters' => [
                    'en' => [
                        'parent_location_id' => 2,
                        'sort_direction' => 'descending',
                        'sort_type' => 'date_published',
                        'query_type' => 'list',
                    ],
                ],
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($query)
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
            'hr'
        );

        self::assertSame(
            [
                'id' => 1,
                'uuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'offset' => 0,
                'limit' => null,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($collection)
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
            'de'
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
            'en'
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
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        self::assertSame(
            [
                'id' => 13,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 1,
                'value' => '42',
                'valueType' => 'my_value_type',
                'config' => ['config' => ['value' => 42]],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($item)
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
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        self::assertSame(
            [
                'id' => 13,
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'position' => 2,
                'value' => '42',
                'valueType' => 'my_value_type',
                'config' => ['config' => ['value' => 42]],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($item)
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
    public function testAddItemToDynamicCollectionInEmptySlot(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->position = 4;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        self::assertSame(
            [
                'id' => 13,
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'position' => 4,
                'value' => '42',
                'valueType' => 'my_value_type',
                'config' => ['config' => ['value' => 42]],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($item)
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
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        self::assertSame(
            [
                'id' => 13,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 3,
                'value' => '42',
                'valueType' => 'my_value_type',
                'config' => ['config' => ['value' => 42]],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($item)
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
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $itemCreateStruct
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
            $itemCreateStruct
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
            $itemCreateStruct
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler::updateItem
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testUpdateItem(): void
    {
        $itemUpdateStruct = new ItemUpdateStruct();
        $itemUpdateStruct->config = [
            'new_config' => [
                'val' => 24,
            ],
        ];

        $item = $this->collectionHandler->updateItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            $itemUpdateStruct
        );

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 0,
                'value' => '72',
                'valueType' => 'my_value_type',
                'config' => ['new_config' => ['val' => 24]],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($item)
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
            2
        );

        self::assertSame(
            [
                'id' => 12,
                'collectionId' => 4,
                'collectionUuid' => '08937ca0-18f4-5806-84df-8c132c36cabe',
                'position' => 2,
                'value' => '74',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedItem)
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
            1
        );

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 1,
                'value' => '72',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedItem)
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
            0
        );

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 0,
                'value' => '72',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedItem)
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
            0
        );

        self::assertSame(
            [
                'id' => 2,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 0,
                'value' => '73',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedItem)
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
            1
        );

        self::assertSame(
            [
                'id' => 3,
                'collectionId' => 1,
                'collectionUuid' => 'a79dde13-1f5c-51a6-bea9-b766236be49e',
                'position' => 1,
                'value' => '74',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedItem)
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
            4
        );

        self::assertSame(
            [
                'id' => 7,
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'position' => 4,
                'value' => '72',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedItem)
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
            2
        );

        self::assertSame(
            [
                'id' => 8,
                'collectionId' => 3,
                'collectionUuid' => 'da050624-8ae0-5fb9-ae85-092bf8242b89',
                'position' => 2,
                'value' => '73',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedItem)
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
            -1
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
            9999
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
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT)
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
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT)
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItems
     */
    public function testDeleteItems(): void
    {
        $collection = $this->collectionHandler->deleteItems(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT)
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

        $createdQuery = $this->collectionHandler->createQuery(
            $collection,
            $queryCreateStruct
        );

        self::assertSame(
            [
                'id' => 5,
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'type' => 'my_query_type',
                'parameters' => [
                    'en' => [
                        'param' => 'value',
                    ],
                    'hr' => [
                        'param' => 'value',
                    ],
                ],
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($createdQuery)
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
            $queryCreateStruct
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
            $translationUpdateStruct
        );

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($updatedQuery)
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
            $translationUpdateStruct
        );

        self::assertSame(
            [
                'id' => 1,
                'collectionId' => 2,
                'collectionUuid' => '45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a',
                'type' => 'my_query_type',
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
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($updatedQuery)
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
            new QueryTranslationUpdateStruct()
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
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED)
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
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );

        $this->addToAssertionCount(1);
    }
}
