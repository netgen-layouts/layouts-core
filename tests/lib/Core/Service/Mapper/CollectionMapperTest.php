<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Item\NullItemDefinition;
use Netgen\BlockManager\Collection\QueryType\NullQueryType;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class CollectionMapperTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    private $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createCollectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollection(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 2,
                'offset' => 10,
                'limit' => 20,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $collection = $this->mapper->mapCollection($persistenceCollection);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertSame(2, $collection->getId());
        $this->assertSame(10, $collection->getOffset());
        $this->assertSame(20, $collection->getLimit());
        $this->assertTrue($collection->isPublished());
        $this->assertTrue($collection->isTranslatable());
        $this->assertSame('en', $collection->getMainLocale());
        $this->assertFalse($collection->isAlwaysAvailable());
        $this->assertSame(['en'], $collection->getAvailableLocales());

        foreach ($collection->getItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getManualItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getOverrideItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        $this->assertSame(
            count($collection->getItems()),
            count($collection->getManualItems()) + count($collection->getOverrideItems())
        );

        $this->assertInstanceOf(APIQuery::class, $collection->getQuery());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocale(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
            ]
        );

        $collection = $this->mapper->mapCollection($persistenceCollection, ['hr']);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        $this->assertSame('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocales(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
            ]
        );

        $collection = $this->mapper->mapCollection($persistenceCollection, ['hr', 'en']);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        $this->assertSame('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
            ]
        );

        $collection = $this->mapper->mapCollection($persistenceCollection, ['fr', 'no']);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        $this->assertSame('en', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "42"
     */
    public function testMapCollectionWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 42,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en', 'hr', 'de'],
            ]
        );

        $this->mapper->mapCollection($persistenceCollection, ['fr', 'no'], false);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "42"
     */
    public function testMapCollectionWithLocalesAndNotAlwaysAvailable(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 42,
                'mainLocale' => 'en',
                'alwaysAvailable' => false,
                'availableLocales' => ['en', 'hr', 'de'],
            ]
        );

        $this->mapper->mapCollection($persistenceCollection, ['fr', 'no']);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithNoQuery(): void
    {
        $persistenceCollection = Collection::fromArray(
            [
                'id' => 1,
                'offset' => 10,
                'limit' => 20,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $collection = $this->mapper->mapCollection($persistenceCollection);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertSame(1, $collection->getId());
        $this->assertSame(0, $collection->getOffset());
        $this->assertSame(20, $collection->getLimit());
        $this->assertTrue($collection->isPublished());
        $this->assertTrue($collection->isTranslatable());
        $this->assertSame('en', $collection->getMainLocale());
        $this->assertFalse($collection->isAlwaysAvailable());
        $this->assertSame(['en'], $collection->getAvailableLocales());

        foreach ($collection->getItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getManualItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getOverrideItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        $this->assertSame(
            count($collection->getItems()),
            count($collection->getManualItems()) + count($collection->getOverrideItems())
        );

        $this->assertNull($collection->getQuery());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapItem
     */
    public function testMapItem(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
                'type' => APIItem::TYPE_OVERRIDE,
                'value' => '12',
                'valueType' => 'my_value_type',
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 42,
                    ],
                ],
            ]
        );

        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->identicalTo('12'), $this->identicalTo('my_value_type'))
            ->will($this->returnValue($cmsItem));

        $item = $this->mapper->mapItem($persistenceItem);

        $this->assertInstanceOf(APIItem::class, $item);
        $this->assertSame(1, $item->getId());
        $this->assertSame(42, $item->getCollectionId());
        $this->assertSame($this->itemDefinitionRegistry->getItemDefinition('my_value_type'), $item->getDefinition());
        $this->assertSame(1, $item->getPosition());
        $this->assertSame(APIItem::TYPE_OVERRIDE, $item->getType());
        $this->assertSame('12', $item->getValue());
        $this->assertSame($cmsItem, $item->getCmsItem());
        $this->assertTrue($item->isPublished());

        $this->assertTrue($item->hasConfig('key'));
        $this->assertInstanceOf(Config::class, $item->getConfig('key'));

        $itemConfig = $item->getConfig('key');

        $this->assertTrue($itemConfig->getParameter('param1')->getValue());
        $this->assertSame(42, $itemConfig->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapItem
     */
    public function testMapItemWithInvalidItemDefinition(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
                'type' => APIItem::TYPE_OVERRIDE,
                'value' => '12',
                'valueType' => 'unknown',
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 42,
                    ],
                ],
            ]
        );

        $cmsItem = new NullCmsItem('value');

        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->identicalTo('12'), $this->identicalTo('null'))
            ->will($this->returnValue($cmsItem));

        $item = $this->mapper->mapItem($persistenceItem);

        $this->assertInstanceOf(APIItem::class, $item);
        $this->assertSame(1, $item->getId());
        $this->assertSame(42, $item->getCollectionId());
        $this->assertInstanceOf(NullItemDefinition::class, $item->getDefinition());
        $this->assertSame(1, $item->getPosition());
        $this->assertSame(APIItem::TYPE_OVERRIDE, $item->getType());
        $this->assertSame('12', $item->getValue());
        $this->assertSame($cmsItem, $item->getCmsItem());
        $this->assertTrue($item->isPublished());

        $this->assertFalse($item->hasConfig('key'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQuery(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
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
            ]
        );

        $query = $this->mapper->mapQuery($persistenceQuery);

        $this->assertSame(
            $this->queryTypeRegistry->getQueryType('my_query_type'),
            $query->getQueryType()
        );

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertSame(1, $query->getId());
        $this->assertSame(42, $query->getCollectionId());
        $this->assertTrue($query->isPublished());
        $this->assertTrue($query->isTranslatable());
        $this->assertSame('en', $query->getMainLocale());
        $this->assertFalse($query->isAlwaysAvailable());
        $this->assertSame(['en'], $query->getAvailableLocales());

        $this->assertSame('value', $query->getParameter('param')->getValue());
        $this->assertNull($query->getParameter('param2')->getValue());

        $this->assertSame('en', $query->getLocale());

        $this->assertSame('value', $query->getParameter('param')->getValue());
        $this->assertNull($query->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocale(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'type' => 'my_query_type',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ]
        );

        $query = $this->mapper->mapQuery($persistenceQuery, ['hr']);

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        $this->assertSame('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocales(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'type' => 'my_query_type',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ]
        );

        $query = $this->mapper->mapQuery($persistenceQuery, ['hr', 'en']);

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        $this->assertSame('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'type' => 'my_query_type',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ]
        );

        $query = $this->mapper->mapQuery($persistenceQuery, ['fr', 'no']);

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        $this->assertSame('en', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "42"
     */
    public function testMapQueryWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 42,
                'type' => 'my_query_type',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ]
        );

        $this->mapper->mapQuery($persistenceQuery, ['fr', 'no'], false);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "42"
     */
    public function testMapQueryWithLocalesAndNotAlwaysAvailable(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 42,
                'type' => 'my_query_type',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
            ]
        );

        $this->mapper->mapQuery($persistenceQuery, ['fr', 'no']);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithInvalidType(): void
    {
        $persistenceQuery = Query::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
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
            ]
        );

        $query = $this->mapper->mapQuery($persistenceQuery);

        $this->assertInstanceOf(NullQueryType::class, $query->getQueryType());

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertSame(1, $query->getId());
        $this->assertSame(42, $query->getCollectionId());
        $this->assertTrue($query->isPublished());
        $this->assertTrue($query->isTranslatable());
        $this->assertSame('en', $query->getMainLocale());
        $this->assertFalse($query->isAlwaysAvailable());
        $this->assertSame(['en'], $query->getAvailableLocales());

        $this->assertFalse($query->hasParameter('param'));
        $this->assertFalse($query->hasParameter('param2'));

        $this->assertSame('en', $query->getLocale());
    }
}
