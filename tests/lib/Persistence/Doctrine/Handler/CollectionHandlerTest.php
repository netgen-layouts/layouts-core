<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryTranslationUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class CollectionHandlerTest extends TestCase
{
    use TestCaseTrait;
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getCollectionSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testLoadCollection(): void
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            [
                'id' => 1,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "999999"
     */
    public function testLoadCollectionThrowsNotFoundException(): void
    {
        $this->collectionHandler->loadCollection(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     */
    public function testLoadItem(): void
    {
        $item = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);

        $this->assertInstanceOf(Item::class, $item);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'position' => 0,
                'type' => Item::TYPE_MANUAL,
                'value' => '72',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find item with identifier "999999"
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->collectionHandler->loadItem(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItemWithPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemWithPositionData
     */
    public function testLoadItemWithPosition(): void
    {
        $item = $this->collectionHandler->loadItemWithPosition(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            0
        );

        $this->assertInstanceOf(Item::class, $item);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'position' => 0,
                'type' => Item::TYPE_MANUAL,
                'value' => '72',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItemWithPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemWithPositionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find item in collection with ID "1" at position 9999
     */
    public function testLoadItemWithPositionThrowsNotFoundException(): void
    {
        $this->collectionHandler->loadItemWithPosition(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItems(): void
    {
        $items = $this->collectionHandler->loadCollectionItems(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );

        $this->assertNotEmpty($items);

        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getQuerySelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     */
    public function testLoadQuery(): void
    {
        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        $this->assertInstanceOf(Query::class, $query);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 2,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "999999"
     */
    public function testLoadQueryThrowsNotFoundException(): void
    {
        $this->collectionHandler->loadQuery(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testLoadCollectionQuery(): void
    {
        $query = $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED)
        );

        $this->assertInstanceOf(Query::class, $query);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 2,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query for collection with identifier "1"
     */
    public function testLoadCollectionQueryThrowsNotFoundException(): void
    {
        $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionExists(): void
    {
        $this->assertTrue($this->collectionHandler->collectionExists(1, Value::STATUS_DRAFT));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExists(): void
    {
        $this->assertFalse($this->collectionHandler->collectionExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExistsInStatus(): void
    {
        $this->assertFalse($this->collectionHandler->collectionExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
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

        $createdCollection = $this->collectionHandler->createCollection($collectionCreateStruct);

        $this->assertInstanceOf(Collection::class, $createdCollection);

        $this->assertSame(
            [
                'id' => 7,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQueryTranslation
     */
    public function testCreateCollectionTranslation(): void
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'en'
        );

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            [
                'id' => 2,
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

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => $collection->id,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationWithNonMainSourceLocale(): void
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'hr'
        );

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            [
                'id' => 2,
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

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => $collection->id,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationForCollectionWithNoQuery(): void
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            'de',
            'en'
        );

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            [
                'id' => 1,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Collection already has the provided locale.
     */
    public function testCreateCollectionTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'en',
            'hr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Collection does not have the provided source locale.
     */
    public function testCreateCollectionTranslationThrowsBadStateExceptionWithNonExistingSourceLocale(): void
    {
        $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'fr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testSetMainTranslation(): void
    {
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $collection = $this->collectionHandler->setMainTranslation($collection, 'hr');

        $this->assertSame('hr', $collection->mainLocale);

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);
        $this->assertSame('hr', $query->mainLocale);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testSetMainTranslationForCollectionWithNoQuery(): void
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
        $collection = $this->collectionHandler->setMainTranslation($collection, 'hr');

        $this->assertSame('hr', $collection->mainLocale);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Collection does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $this->collectionHandler->setMainTranslation($collection, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
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

        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertSame(
            [
                'id' => 1,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
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

        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertSame(
            [
                'id' => 3,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollectionWithDefaultValues(): void
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        $updatedCollection = $this->collectionHandler->updateCollection(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $collectionUpdateStruct
        );

        $this->assertInstanceOf(Collection::class, $updatedCollection);

        $this->assertSame(
            [
                'id' => 1,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testCopyCollection(): void
    {
        $copiedCollection = $this->collectionHandler->copyCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED)
        );

        $this->assertInstanceOf(Collection::class, $copiedCollection);

        $this->assertSame(
            [
                'id' => 7,
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

        $this->assertSame(
            [
                [
                    'id' => 13,
                    'collectionId' => $copiedCollection->id,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_PUBLISHED,
                    'config' => [],
                ],
                [
                    'id' => 14,
                    'collectionId' => $copiedCollection->id,
                    'position' => 3,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_PUBLISHED,
                    'config' => [],
                ],
                [
                    'id' => 15,
                    'collectionId' => $copiedCollection->id,
                    'position' => 5,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_PUBLISHED,
                    'config' => [],
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
        );

        $query = $this->collectionHandler->loadCollectionQuery($copiedCollection);

        $this->assertSame(
            [
                'id' => 5,
                'collectionId' => $copiedCollection->id,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testCopyCollectionWithoutQuery(): void
    {
        $copiedCollection = $this->collectionHandler->copyCollection(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );

        $this->assertInstanceOf(Collection::class, $copiedCollection);

        $this->assertSame(
            [
                'id' => 7,
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

        $this->assertSame(
            [
                [
                    'id' => 13,
                    'collectionId' => $copiedCollection->id,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_DRAFT,
                    'config' => [],
                ],
                [
                    'id' => 14,
                    'collectionId' => $copiedCollection->id,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_DRAFT,
                    'config' => [],
                ],
                [
                    'id' => 15,
                    'collectionId' => $copiedCollection->id,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_DRAFT,
                    'config' => [],
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testCreateCollectionStatus(): void
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            Value::STATUS_ARCHIVED
        );

        $this->assertInstanceOf(Collection::class, $copiedCollection);

        $this->assertSame(
            [
                'id' => 3,
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

        $this->assertSame(
            [
                [
                    'id' => 7,
                    'collectionId' => 3,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_ARCHIVED,
                    'config' => [],
                ],
                [
                    'id' => 8,
                    'collectionId' => 3,
                    'position' => 3,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_ARCHIVED,
                    'config' => [],
                ],
                [
                    'id' => 9,
                    'collectionId' => 3,
                    'position' => 5,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_ARCHIVED,
                    'config' => [],
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
        );

        $this->assertSame(
            [
                'id' => 2,
                'collectionId' => 3,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testCreateCollectionStatusWithoutQuery(): void
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            Value::STATUS_ARCHIVED
        );

        $this->assertInstanceOf(Collection::class, $copiedCollection);

        $this->assertSame(
            [
                'id' => 1,
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

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_ARCHIVED,
                    'config' => [],
                ],
                [
                    'id' => 2,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '73',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_ARCHIVED,
                    'config' => [],
                ],
                [
                    'id' => 3,
                    'collectionId' => 1,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '74',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_ARCHIVED,
                    'config' => [],
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionItems($copiedCollection)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "3"
     */
    public function testDeleteCollection(): void
    {
        $this->collectionHandler->deleteCollection(3);

        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "1"
     */
    public function testDeleteCollectionWithoutQuery(): void
    {
        $this->collectionHandler->deleteCollection(1);

        $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "3"
     */
    public function testDeleteCollectionInOneStatus(): void
    {
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQueryTranslations
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionTranslation(): void
    {
        $collection = $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'hr'
        );

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            [
                'id' => 2,
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

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => $collection->id,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     */
    public function testDeleteCollectionTranslationForCollectionWithNoQuery(): void
    {
        $collection = $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            'hr'
        );

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            [
                'id' => 1,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Collection does not have the provided locale.
     */
    public function testDeleteCollectionTranslationWithNonExistingLocale(): void
    {
        $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Main translation cannot be removed from the collection.
     */
    public function testDeleteCollectionTranslationWithMainLocale(): void
    {
        $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'en'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItem(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 1;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        $this->assertInstanceOf(Item::class, $item);

        $this->assertSame(
            [
                'id' => 13,
                'collectionId' => 1,
                'position' => 1,
                'type' => Item::TYPE_MANUAL,
                'value' => '42',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => ['config' => ['value' => 42]],
            ],
            $this->exportObject($item)
        );

        $secondItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertSame(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemToDynamicCollection(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 2;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        $this->assertInstanceOf(Item::class, $item);

        $this->assertSame(
            [
                'id' => 13,
                'collectionId' => 3,
                'position' => 2,
                'type' => Item::TYPE_MANUAL,
                'value' => '42',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => ['config' => ['value' => 42]],
            ],
            $this->exportObject($item)
        );

        $secondItem = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
        $this->assertSame(3, $secondItem->position);

        $thirdItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        $this->assertSame(4, $thirdItem->position);

        $fourthItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        $this->assertSame(5, $fourthItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemToDynamicCollectionInEmptySlot(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 4;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        $this->assertInstanceOf(Item::class, $item);

        $this->assertSame(
            [
                'id' => 13,
                'collectionId' => 3,
                'position' => 4,
                'type' => Item::TYPE_MANUAL,
                'value' => '42',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => ['config' => ['value' => 42]],
            ],
            $this->exportObject($item)
        );

        $secondItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        $this->assertSame(5, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemWithNoPosition(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = ['config' => ['value' => 42]];

        $item = $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );

        $this->assertInstanceOf(Item::class, $item);

        $this->assertSame(
            [
                'id' => 13,
                'collectionId' => 1,
                'position' => 3,
                'type' => Item::TYPE_MANUAL,
                'value' => '42',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => ['config' => ['value' => 42]],
            ],
            $this->exportObject($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage When adding items to dynamic collections, position is mandatory.
     */
    public function testAddItemToDynamicCollectionWithoutPositionThrowsBadStateException(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
            $itemCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testAddItemThrowsBadStateExceptionOnNegativePosition(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testAddItemThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
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

        $this->assertInstanceOf(Item::class, $item);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'position' => 0,
                'type' => Item::TYPE_MANUAL,
                'value' => '72',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => ['new_config' => ['val' => 24]],
            ],
            $this->exportObject($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItem(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(12, Value::STATUS_DRAFT),
            2
        );

        $this->assertInstanceOf(Item::class, $movedItem);

        $this->assertSame(
            [
                'id' => 12,
                'collectionId' => 4,
                'position' => 2,
                'type' => Item::TYPE_MANUAL,
                'value' => '74',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($movedItem)
        );

        $firstItem = $this->collectionHandler->loadItem(10, Value::STATUS_DRAFT);
        $this->assertSame(3, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(11, Value::STATUS_DRAFT);
        $this->assertSame(4, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemWithSwitchingPositions(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            1
        );

        $this->assertInstanceOf(Item::class, $movedItem);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'position' => 1,
                'type' => Item::TYPE_MANUAL,
                'value' => '72',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($movedItem)
        );

        $firstItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertSame(0, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        $this->assertSame(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToSamePosition(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            0
        );

        $this->assertInstanceOf(Item::class, $movedItem);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 1,
                'position' => 0,
                'type' => Item::TYPE_MANUAL,
                'value' => '72',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($movedItem)
        );

        $firstItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertSame(1, $firstItem->position);

        $firstItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        $this->assertSame(2, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPosition(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT),
            0
        );

        $this->assertInstanceOf(Item::class, $movedItem);

        $this->assertSame(
            [
                'id' => 2,
                'collectionId' => 1,
                'position' => 0,
                'type' => Item::TYPE_MANUAL,
                'value' => '73',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($movedItem)
        );

        $firstItem = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);
        $this->assertSame(1, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPositionWithSwitchingPositions(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT),
            1
        );

        $this->assertInstanceOf(Item::class, $movedItem);

        $this->assertSame(
            [
                'id' => 3,
                'collectionId' => 1,
                'position' => 1,
                'type' => Item::TYPE_MANUAL,
                'value' => '74',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($movedItem)
        );

        $firstItem = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);
        $this->assertSame(0, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertSame(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemInDynamicCollection(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT),
            4
        );

        $this->assertInstanceOf(Item::class, $movedItem);

        $this->assertSame(
            [
                'id' => 7,
                'collectionId' => 3,
                'position' => 4,
                'type' => Item::TYPE_MANUAL,
                'value' => '72',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($movedItem)
        );

        $secondItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        $this->assertSame(3, $secondItem->position);

        $thirdItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        $this->assertSame(5, $thirdItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPositionInDynamicCollection(): void
    {
        $movedItem = $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT),
            2
        );

        $this->assertInstanceOf(Item::class, $movedItem);

        $this->assertSame(
            [
                'id' => 8,
                'collectionId' => 3,
                'position' => 2,
                'type' => Item::TYPE_MANUAL,
                'value' => '73',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
            $this->exportObject($movedItem)
        );

        $firstItem = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
        $this->assertSame(3, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testMoveItemThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveItemThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testSwitchItemPositions(): void
    {
        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);

        $updatedItem1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $updatedItem2 = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);

        $this->assertSame($item2->position, $updatedItem1->position);
        $this->assertSame($item1->position, $updatedItem2->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage First and second items are the same.
     */
    public function testSwitchItemPositionsThrowsBadStateExceptionWithSameItem(): void
    {
        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Positions can be switched only for items within the same collection.
     */
    public function testSwitchItemPositionsThrowsBadStateExceptionWithItemsFromDifferentCollections(): void
    {
        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     */
    public function testDeleteItem(): void
    {
        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT)
        );

        $secondItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        $this->assertSame(1, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     */
    public function testDeleteItemFromDynamicCollection(): void
    {
        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT)
        );

        $secondItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        $this->assertSame(3, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItems
     */
    public function testDeleteItems(): void
    {
        $collection = $this->collectionHandler->deleteItems(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT)
        );

        $this->assertCount(0, $this->collectionHandler->loadCollectionItems($collection));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItems
     */
    public function testDeleteItemsWithSpecificItemType(): void
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_OVERRIDE;
        $itemCreateStruct->position = 2;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'my_value_type';
        $itemCreateStruct->config = [];

        $collection = $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT);

        $this->collectionHandler->addItem($collection, $itemCreateStruct);

        $collection = $this->collectionHandler->deleteItems($collection, Item::TYPE_OVERRIDE);

        $collectionItems = $this->collectionHandler->loadCollectionItems($collection);
        $this->assertCount(3, $collectionItems);

        foreach ($collectionItems as $item) {
            $this->assertSame(Item::TYPE_MANUAL, $item->type);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
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

        $this->assertInstanceOf(Query::class, $createdQuery);

        $this->assertSame(
            [
                'id' => 5,
                'collectionId' => $collection->id,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Provided collection already has a query.
     */
    public function testCreateQueryThrowsBadStateExceptionWithExistingQuery(): void
    {
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
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

        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 2,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     */
    public function testUpdateQueryTranslationWithDefaultValues(): void
    {
        $translationUpdateStruct = new QueryTranslationUpdateStruct();

        $updatedQuery = $this->collectionHandler->updateQueryTranslation(
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
            'en',
            $translationUpdateStruct
        );

        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertSame(
            [
                'id' => 1,
                'collectionId' => 2,
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Query does not have the provided locale.
     */
    public function testUpdateQueryTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->collectionHandler->updateQueryTranslation(
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
            'de',
            new QueryTranslationUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "2"
     */
    public function testDeleteCollectionQuery(): void
    {
        $this->collectionHandler->deleteCollectionQuery(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED)
        );

        // Query with ID 2 was in the collection with ID 3
        $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionQueryWithNoQuery(): void
    {
        $this->collectionHandler->deleteCollectionQuery(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );

        $this->assertTrue(true);
    }
}
