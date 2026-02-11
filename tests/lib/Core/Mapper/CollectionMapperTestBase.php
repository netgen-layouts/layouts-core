<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Collection\Query as APIQuery;
use Netgen\Layouts\Collection\Item\NullItemDefinition;
use Netgen\Layouts\Collection\QueryType\NullQueryType;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Collection\Slot;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Netgen\Layouts\Tests\Core\CoreTestCase;

abstract class CollectionMapperTestBase extends CoreTestCase
{
    final public function testMapCollection(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => PersistenceStatus::Published,
            ],
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $collection->id->toString());
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $collection->blockId->toString());
        self::assertSame(10, $collection->offset);
        self::assertSame(20, $collection->limit);
        self::assertTrue($collection->isPublished);
        self::assertTrue($collection->isTranslatable);
        self::assertSame('en', $collection->mainLocale);
        self::assertFalse($collection->isAlwaysAvailable);
        self::assertSame(['en'], $collection->availableLocales);

        self::assertCount(3, $collection->items);
        self::assertCount(2, $collection->slots);
        self::assertInstanceOf(APIQuery::class, $collection->query);
    }

    final public function testMapCollectionWithLocale(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'status' => PersistenceStatus::Published,
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, ['hr']);

        self::assertSame(['en', 'hr', 'de'], $collection->availableLocales);
        self::assertSame('hr', $collection->locale);
    }

    final public function testMapCollectionWithLocales(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'status' => PersistenceStatus::Published,
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, ['hr', 'en']);

        self::assertSame(['en', 'hr', 'de'], $collection->availableLocales);
        self::assertSame('hr', $collection->locale);
    }

    final public function testMapCollectionWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'status' => PersistenceStatus::Published,
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'mainLocale' => 'en',
                'offset' => 10,
                'limit' => 20,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, ['fr', 'no']);

        self::assertSame(['en', 'hr', 'de'], $collection->availableLocales);
        self::assertSame('en', $collection->locale);
    }

    final public function testMapCollectionWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "f06f245a-f951-52c8-bfa3-84c80154eadc"');

        $persistenceCollection = Collection::fromArray(
            [
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'mainLocale' => 'en',
                'isAlwaysAvailable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $this->collectionMapper->mapCollection($persistenceCollection, ['fr', 'no'], false);
    }

    final public function testMapCollectionWithLocalesAndNotAlwaysAvailable(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "f06f245a-f951-52c8-bfa3-84c80154eadc"');

        $persistenceCollection = Collection::fromArray(
            [
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'mainLocale' => 'en',
                'isAlwaysAvailable' => false,
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $this->collectionMapper->mapCollection($persistenceCollection, ['fr', 'no']);
    }

    final public function testMapCollectionWithNoQuery(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 1,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => PersistenceStatus::Published,
            ],
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $collection->id->toString());
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $collection->blockId->toString());
        self::assertSame(0, $collection->offset);
        self::assertSame(20, $collection->limit);
        self::assertTrue($collection->isPublished);
        self::assertTrue($collection->isTranslatable);
        self::assertSame('en', $collection->mainLocale);
        self::assertFalse($collection->isAlwaysAvailable);
        self::assertSame(['en'], $collection->availableLocales);

        self::assertEmpty($collection->items);
        self::assertNull($collection->query);
    }

    final public function testMapItem(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 1,
                'value' => '12',
                'valueType' => 'test_value_type',
                'viewType' => 'overlay',
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 42,
                    ],
                ],
            ],
        );

        $cmsItem = new CmsItem();

        $this->cmsItemLoaderStub
            ->method('load')
            ->willReturn($cmsItem);

        $item = $this->collectionMapper->mapItem($persistenceItem);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $item->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->collectionId->toString());
        self::assertSame($this->itemDefinitionRegistry->getItemDefinition('test_value_type'), $item->definition);
        self::assertSame(1, $item->position);
        self::assertSame('12', $item->value);
        self::assertSame('overlay', $item->viewType);
        self::assertSame($cmsItem, $item->cmsItem);
        self::assertTrue($item->isPublished);

        self::assertTrue($item->hasConfig('key'));

        $itemConfig = $item->getConfig('key');

        self::assertTrue($itemConfig->getParameter('param1')->value);
        self::assertSame(42, $itemConfig->getParameter('param2')->value);
    }

    final public function testMapItemWithNullItemValue(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 1,
                'value' => null,
                'valueType' => 'test_value_type',
                'viewType' => 'overlay',
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 42,
                    ],
                ],
            ],
        );

        $item = $this->collectionMapper->mapItem($persistenceItem);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $item->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->collectionId->toString());
        self::assertSame($this->itemDefinitionRegistry->getItemDefinition('test_value_type'), $item->definition);
        self::assertSame(1, $item->position);
        self::assertNull($item->value);
        self::assertSame('overlay', $item->viewType);
        self::assertInstanceOf(NullCmsItem::class, $item->cmsItem);
        self::assertSame('test_value_type', $item->cmsItem->valueType);
        self::assertTrue($item->isPublished);

        self::assertTrue($item->hasConfig('key'));

        $itemConfig = $item->getConfig('key');

        self::assertTrue($itemConfig->getParameter('param1')->value);
        self::assertSame(42, $itemConfig->getParameter('param2')->value);
    }

    final public function testMapItemWithInvalidItemDefinition(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 1,
                'value' => '12',
                'valueType' => 'unknown',
                'viewType' => null,
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 42,
                    ],
                ],
            ],
        );

        $cmsItem = new NullCmsItem('value');

        $this->cmsItemLoaderStub
            ->method('load')
            ->willReturn($cmsItem);

        $item = $this->collectionMapper->mapItem($persistenceItem);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $item->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->collectionId->toString());
        self::assertInstanceOf(NullItemDefinition::class, $item->definition);
        self::assertSame(1, $item->position);
        self::assertSame('12', $item->value);
        self::assertNull($item->viewType);
        self::assertSame($cmsItem, $item->cmsItem);
        self::assertTrue($item->isPublished);

        self::assertFalse($item->hasConfig('key'));
    }

    final public function testMapQuery(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'test_query_type',
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => [
                    'en' => [
                        'param' => 'value',
                    ],
                ],
            ],
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery);

        self::assertSame(
            $this->queryTypeRegistry->getQueryType('test_query_type'),
            $query->queryType,
        );

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $query->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $query->collectionId->toString());
        self::assertTrue($query->isPublished);
        self::assertTrue($query->isTranslatable);
        self::assertSame('en', $query->mainLocale);
        self::assertFalse($query->isAlwaysAvailable);
        self::assertSame(['en'], $query->availableLocales);

        self::assertSame('value', $query->getParameter('param')->value);
        self::assertNull($query->getParameter('param2')->value);

        self::assertSame('en', $query->locale);

        self::assertSame('value', $query->getParameter('param')->value);
        self::assertNull($query->getParameter('param2')->value);
    }

    final public function testMapQueryWithLocale(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'test_query_type',
                'isAlwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'isTranslatable' => true,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery, ['hr']);

        self::assertSame(['en', 'hr', 'de'], $query->availableLocales);
        self::assertSame('hr', $query->locale);
    }

    final public function testMapQueryWithLocales(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'test_query_type',
                'isAlwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'isTranslatable' => true,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery, ['hr', 'en']);

        self::assertSame(['en', 'hr', 'de'], $query->availableLocales);
        self::assertSame('hr', $query->locale);
    }

    final public function testMapQueryWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'test_query_type',
                'isAlwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'isTranslatable' => true,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery, ['fr', 'no']);

        self::assertSame(['en', 'hr', 'de'], $query->availableLocales);
        self::assertSame('en', $query->locale);
    }

    final public function testMapQueryWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "4adf0f00-f6c2-5297-9f96-039bfabe8d3b"');

        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'test_query_type',
                'isAlwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $this->collectionMapper->mapQuery($persistenceQuery, ['fr', 'no'], false);
    }

    final public function testMapQueryWithLocalesAndNotAlwaysAvailable(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "4adf0f00-f6c2-5297-9f96-039bfabe8d3b"');

        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'test_query_type',
                'isAlwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $this->collectionMapper->mapQuery($persistenceQuery, ['fr', 'no']);
    }

    final public function testMapQueryWithInvalidType(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'unknown',
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => [
                    'en' => [
                        'param' => 'value',
                    ],
                ],
            ],
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery);

        self::assertInstanceOf(NullQueryType::class, $query->queryType);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $query->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $query->collectionId->toString());
        self::assertTrue($query->isPublished);
        self::assertTrue($query->isTranslatable);
        self::assertSame('en', $query->mainLocale);
        self::assertFalse($query->isAlwaysAvailable);
        self::assertSame(['en'], $query->availableLocales);

        self::assertFalse($query->hasParameter('param'));
        self::assertFalse($query->hasParameter('param2'));

        self::assertSame('en', $query->locale);
    }

    final public function testMapSlot(): void
    {
        $persistenceSlot = Slot::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => PersistenceStatus::Published,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 1,
                'viewType' => 'overlay',
            ],
        );

        $slot = $this->collectionMapper->mapSlot($persistenceSlot);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $slot->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $slot->collectionId->toString());
        self::assertSame(1, $slot->position);
        self::assertSame('overlay', $slot->viewType);
        self::assertTrue($slot->isPublished);
    }
}
