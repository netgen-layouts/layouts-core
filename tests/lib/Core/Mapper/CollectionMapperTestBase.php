<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Collection\Query as APIQuery;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Collection\Item\NullItemDefinition;
use Netgen\Layouts\Collection\QueryType\NullQueryType;
use Netgen\Layouts\Core\Mapper\CollectionMapper;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Collection\Slot;
use Netgen\Layouts\Tests\Core\CoreTestCase;

abstract class CollectionMapperTestBase extends CoreTestCase
{
    private CollectionMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createCollectionMapper();
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollection(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        $collection = $this->mapper->mapCollection($persistenceCollection);

        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $collection->getId()->toString());
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $collection->getBlockId()->toString());
        self::assertSame(10, $collection->getOffset());
        self::assertSame(20, $collection->getLimit());
        self::assertTrue($collection->isPublished());
        self::assertTrue($collection->isTranslatable());
        self::assertSame('en', $collection->getMainLocale());
        self::assertFalse($collection->isAlwaysAvailable());
        self::assertSame(['en'], $collection->getAvailableLocales());

        self::assertCount(3, $collection->getItems());
        self::assertCount(2, $collection->getSlots());
        self::assertInstanceOf(APIQuery::class, $collection->getQuery());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocale(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'status' => Value::STATUS_PUBLISHED,
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $collection = $this->mapper->mapCollection($persistenceCollection, ['hr']);

        self::assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        self::assertSame('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocales(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'status' => Value::STATUS_PUBLISHED,
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $collection = $this->mapper->mapCollection($persistenceCollection, ['hr', 'en']);

        self::assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        self::assertSame('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'status' => Value::STATUS_PUBLISHED,
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'mainLocale' => 'en',
                'offset' => 10,
                'limit' => 20,
                'alwaysAvailable' => true,
                'isTranslatable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $collection = $this->mapper->mapCollection($persistenceCollection, ['fr', 'no']);

        self::assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        self::assertSame('en', $collection->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "f06f245a-f951-52c8-bfa3-84c80154eadc"');

        $persistenceCollection = Collection::fromArray(
            [
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $this->mapper->mapCollection($persistenceCollection, ['fr', 'no'], false);
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocalesAndNotAlwaysAvailable(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find collection with identifier "f06f245a-f951-52c8-bfa3-84c80154eadc"');

        $persistenceCollection = Collection::fromArray(
            [
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'mainLocale' => 'en',
                'alwaysAvailable' => false,
                'availableLocales' => ['en', 'hr', 'de'],
            ],
        );

        $this->mapper->mapCollection($persistenceCollection, ['fr', 'no']);
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithNoQuery(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 1,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockId' => 42,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'offset' => 10,
                'limit' => 20,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        $collection = $this->mapper->mapCollection($persistenceCollection);

        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $collection->getId()->toString());
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $collection->getBlockId()->toString());
        self::assertSame(0, $collection->getOffset());
        self::assertSame(20, $collection->getLimit());
        self::assertTrue($collection->isPublished());
        self::assertTrue($collection->isTranslatable());
        self::assertSame('en', $collection->getMainLocale());
        self::assertFalse($collection->isAlwaysAvailable());
        self::assertSame(['en'], $collection->getAvailableLocales());

        self::assertEmpty($collection->getItems());
        self::assertNull($collection->getQuery());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapItem
     */
    public function testMapItem(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 1,
                'value' => '12',
                'valueType' => 'my_value_type',
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

        $this->cmsItemLoaderMock
            ->method('load')
            ->with(self::identicalTo('12'), self::identicalTo('my_value_type'))
            ->willReturn($cmsItem);

        $item = $this->mapper->mapItem($persistenceItem);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $item->getId()->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->getCollectionId()->toString());
        self::assertSame($this->itemDefinitionRegistry->getItemDefinition('my_value_type'), $item->getDefinition());
        self::assertSame(1, $item->getPosition());
        self::assertSame('12', $item->getValue());
        self::assertSame('overlay', $item->getViewType());
        self::assertSame($cmsItem, $item->getCmsItem());
        self::assertTrue($item->isPublished());

        self::assertTrue($item->hasConfig('key'));

        $itemConfig = $item->getConfig('key');

        self::assertTrue($itemConfig->getParameter('param1')->getValue());
        self::assertSame(42, $itemConfig->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapItem
     */
    public function testMapItemWithNullItemValue(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 1,
                'value' => null,
                'valueType' => 'my_value_type',
                'viewType' => 'overlay',
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 42,
                    ],
                ],
            ],
        );

        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $item = $this->mapper->mapItem($persistenceItem);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $item->getId()->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->getCollectionId()->toString());
        self::assertSame($this->itemDefinitionRegistry->getItemDefinition('my_value_type'), $item->getDefinition());
        self::assertSame(1, $item->getPosition());
        self::assertNull($item->getValue());
        self::assertSame('overlay', $item->getViewType());
        self::assertInstanceOf(NullCmsItem::class, $item->getCmsItem());
        self::assertSame('my_value_type', $item->getCmsItem()->getValueType());
        self::assertTrue($item->isPublished());

        self::assertTrue($item->hasConfig('key'));

        $itemConfig = $item->getConfig('key');

        self::assertTrue($itemConfig->getParameter('param1')->getValue());
        self::assertSame(42, $itemConfig->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapItem
     */
    public function testMapItemWithInvalidItemDefinition(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
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

        $this->cmsItemLoaderMock
            ->method('load')
            ->with(self::identicalTo('12'), self::identicalTo('null'))
            ->willReturn($cmsItem);

        $item = $this->mapper->mapItem($persistenceItem);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $item->getId()->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->getCollectionId()->toString());
        self::assertInstanceOf(NullItemDefinition::class, $item->getDefinition());
        self::assertSame(1, $item->getPosition());
        self::assertSame('12', $item->getValue());
        self::assertNull($item->getViewType());
        self::assertSame($cmsItem, $item->getCmsItem());
        self::assertTrue($item->isPublished());

        self::assertFalse($item->hasConfig('key'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQuery(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'my_query_type',
                'alwaysAvailable' => false,
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

        $query = $this->mapper->mapQuery($persistenceQuery);

        self::assertSame(
            $this->queryTypeRegistry->getQueryType('my_query_type'),
            $query->getQueryType(),
        );

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $query->getId()->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $query->getCollectionId()->toString());
        self::assertTrue($query->isPublished());
        self::assertTrue($query->isTranslatable());
        self::assertSame('en', $query->getMainLocale());
        self::assertFalse($query->isAlwaysAvailable());
        self::assertSame(['en'], $query->getAvailableLocales());

        self::assertSame('value', $query->getParameter('param')->getValue());
        self::assertNull($query->getParameter('param2')->getValue());

        self::assertSame('en', $query->getLocale());

        self::assertSame('value', $query->getParameter('param')->getValue());
        self::assertNull($query->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocale(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'my_query_type',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'isTranslatable' => true,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $query = $this->mapper->mapQuery($persistenceQuery, ['hr']);

        self::assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        self::assertSame('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocales(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'my_query_type',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'isTranslatable' => true,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $query = $this->mapper->mapQuery($persistenceQuery, ['hr', 'en']);

        self::assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        self::assertSame('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'my_query_type',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'isTranslatable' => true,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $query = $this->mapper->mapQuery($persistenceQuery, ['fr', 'no']);

        self::assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        self::assertSame('en', $query->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "4adf0f00-f6c2-5297-9f96-039bfabe8d3b"');

        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'my_query_type',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $this->mapper->mapQuery($persistenceQuery, ['fr', 'no'], false);
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocalesAndNotAlwaysAvailable(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find query with identifier "4adf0f00-f6c2-5297-9f96-039bfabe8d3b"');

        $persistenceQuery = Query::fromArray(
            [
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'my_query_type',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ],
        );

        $this->mapper->mapQuery($persistenceQuery, ['fr', 'no']);
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithInvalidType(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'unknown',
                'alwaysAvailable' => false,
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

        $query = $this->mapper->mapQuery($persistenceQuery);

        self::assertInstanceOf(NullQueryType::class, $query->getQueryType());

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $query->getId()->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $query->getCollectionId()->toString());
        self::assertTrue($query->isPublished());
        self::assertTrue($query->isTranslatable());
        self::assertSame('en', $query->getMainLocale());
        self::assertFalse($query->isAlwaysAvailable());
        self::assertSame(['en'], $query->getAvailableLocales());

        self::assertFalse($query->hasParameter('param'));
        self::assertFalse($query->hasParameter('param2'));

        self::assertSame('en', $query->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\CollectionMapper::mapSlot
     */
    public function testMapSlot(): void
    {
        $persistenceSlot = Slot::fromArray(
            [
                'id' => 1,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 1,
                'viewType' => 'overlay',
            ],
        );

        $slot = $this->mapper->mapSlot($persistenceSlot);

        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $slot->getId()->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $slot->getCollectionId()->toString());
        self::assertSame(1, $slot->getPosition());
        self::assertSame('overlay', $slot->getViewType());
        self::assertTrue($slot->isPublished());
    }
}
