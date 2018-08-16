<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Mapper;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Item\NullItemDefinition;
use Netgen\BlockManager\Collection\QueryType\NullQueryType;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Tests\Core\CoreTestCase;

abstract class CollectionMapperTest extends CoreTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Mapper\CollectionMapper
     */
    private $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createCollectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapCollection
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

        self::assertSame(2, $collection->getId());
        self::assertSame(10, $collection->getOffset());
        self::assertSame(20, $collection->getLimit());
        self::assertTrue($collection->isPublished());
        self::assertTrue($collection->isTranslatable());
        self::assertSame('en', $collection->getMainLocale());
        self::assertFalse($collection->isAlwaysAvailable());
        self::assertSame(['en'], $collection->getAvailableLocales());

        self::assertCount(3, $collection->getItems());
        self::assertInstanceOf(APIQuery::class, $collection->getQuery());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapCollection
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

        self::assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        self::assertSame('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapCollection
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

        self::assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        self::assertSame('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapCollection
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

        self::assertSame(['en', 'hr', 'de'], $collection->getAvailableLocales());
        self::assertSame('en', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapCollection
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
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapCollection
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
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapCollection
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

        self::assertSame(1, $collection->getId());
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
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapItem
     */
    public function testMapItem(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
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
            ->expects(self::any())
            ->method('load')
            ->with(self::identicalTo('12'), self::identicalTo('my_value_type'))
            ->will(self::returnValue($cmsItem));

        $item = $this->mapper->mapItem($persistenceItem);

        self::assertSame(1, $item->getId());
        self::assertSame(42, $item->getCollectionId());
        self::assertSame($this->itemDefinitionRegistry->getItemDefinition('my_value_type'), $item->getDefinition());
        self::assertSame(1, $item->getPosition());
        self::assertSame('12', $item->getValue());
        self::assertSame($cmsItem, $item->getCmsItem());
        self::assertTrue($item->isPublished());

        self::assertTrue($item->hasConfig('key'));

        $itemConfig = $item->getConfig('key');

        self::assertTrue($itemConfig->getParameter('param1')->getValue());
        self::assertSame(42, $itemConfig->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapItem
     */
    public function testMapItemWithInvalidItemDefinition(): void
    {
        $persistenceItem = Item::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
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
            ->expects(self::any())
            ->method('load')
            ->with(self::identicalTo('12'), self::identicalTo('null'))
            ->will(self::returnValue($cmsItem));

        $item = $this->mapper->mapItem($persistenceItem);

        self::assertSame(1, $item->getId());
        self::assertSame(42, $item->getCollectionId());
        self::assertInstanceOf(NullItemDefinition::class, $item->getDefinition());
        self::assertSame(1, $item->getPosition());
        self::assertSame('12', $item->getValue());
        self::assertSame($cmsItem, $item->getCmsItem());
        self::assertTrue($item->isPublished());

        self::assertFalse($item->hasConfig('key'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapQuery
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

        self::assertSame(
            $this->queryTypeRegistry->getQueryType('my_query_type'),
            $query->getQueryType()
        );

        self::assertSame(1, $query->getId());
        self::assertSame(42, $query->getCollectionId());
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
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapQuery
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

        self::assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        self::assertSame('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapQuery
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

        self::assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        self::assertSame('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapQuery
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

        self::assertSame(['en', 'hr', 'de'], $query->getAvailableLocales());
        self::assertSame('en', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapQuery
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
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapQuery
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
     * @covers \Netgen\BlockManager\Core\Mapper\CollectionMapper::mapQuery
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

        self::assertInstanceOf(NullQueryType::class, $query->getQueryType());

        self::assertSame(1, $query->getId());
        self::assertSame(42, $query->getCollectionId());
        self::assertTrue($query->isPublished());
        self::assertTrue($query->isTranslatable());
        self::assertSame('en', $query->getMainLocale());
        self::assertFalse($query->isAlwaysAvailable());
        self::assertSame(['en'], $query->getAvailableLocales());

        self::assertFalse($query->hasParameter('param'));
        self::assertFalse($query->hasParameter('param2'));

        self::assertSame('en', $query->getLocale());
    }
}
