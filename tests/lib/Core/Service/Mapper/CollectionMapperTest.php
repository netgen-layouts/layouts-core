<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class CollectionMapperTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionMapper = $this->createCollectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollection()
    {
        $persistenceCollection = new Collection(
            array(
                'id' => 2,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(2, $collection->getId());
        $this->assertEquals(APICollection::TYPE_DYNAMIC, $collection->getType());
        $this->assertEquals(Value::STATUS_PUBLISHED, $collection->getStatus());
        $this->assertTrue($collection->isPublished());
        $this->assertTrue($collection->isTranslatable());
        $this->assertEquals('en', $collection->getMainLocale());
        $this->assertFalse($collection->isAlwaysAvailable());
        $this->assertEquals(array('en'), $collection->getAvailableLocales());

        foreach ($collection->getItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getManualItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getOverrideItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        $this->assertEquals(
            count($collection->getItems()),
            count($collection->getManualItems()) + count($collection->getOverrideItems())
        );

        $this->assertInstanceOf(APIQuery::class, $collection->getQuery());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getCollectionLocales
     */
    public function testMapCollectionWithLocales()
    {
        $persistenceCollection = new Collection(
            array(
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, array('hr'));

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('hr'), $collection->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getCollectionLocales
     */
    public function testMapCollectionWithAlwaysAvailableCollection()
    {
        $persistenceCollection = new Collection(
            array(
                'alwaysAvailable' => true,
                'mainLocale' => 'de',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('en', 'hr', 'de'), $collection->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getCollectionLocales
     */
    public function testMapCollectionWithNotAlwaysAvailableCollection()
    {
        $persistenceCollection = new Collection(
            array(
                'alwaysAvailable' => false,
                'mainLocale' => 'de',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('en', 'hr'), $collection->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getCollectionLocales
     */
    public function testMapCollectionWithLocalesAndNoContext()
    {
        $persistenceCollection = new Collection(
            array(
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, array('hr'), false);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('hr'), $collection->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getCollectionLocales
     */
    public function testMapCollectionWithNoContext()
    {
        $persistenceCollection = new Collection(
            array(
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, null, false);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('en', 'hr', 'de'), $collection->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getCollectionLocales
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "42"
     */
    public function testMapCollectionWithNoLocales()
    {
        $persistenceCollection = new Collection(
            array(
                'id' => 42,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $this->collectionMapper->mapCollection($persistenceCollection, array('fr'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithNoQuery()
    {
        $persistenceCollection = new Collection(
            array(
                'id' => 1,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(1, $collection->getId());
        $this->assertEquals(APICollection::TYPE_MANUAL, $collection->getType());
        $this->assertEquals(Value::STATUS_PUBLISHED, $collection->getStatus());
        $this->assertTrue($collection->isPublished());
        $this->assertTrue($collection->isTranslatable());
        $this->assertEquals('en', $collection->getMainLocale());
        $this->assertFalse($collection->isAlwaysAvailable());
        $this->assertEquals(array('en'), $collection->getAvailableLocales());

        foreach ($collection->getItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getManualItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getOverrideItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        $this->assertEquals(
            count($collection->getItems()),
            count($collection->getManualItems()) + count($collection->getOverrideItems())
        );

        $this->assertNull($collection->getQuery());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapItem
     */
    public function testMapItem()
    {
        $persistenceItem = new Item(
            array(
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
                'type' => APIItem::TYPE_OVERRIDE,
                'valueId' => '12',
                'valueType' => 'ezcontent',
            )
        );

        $item = $this->collectionMapper->mapItem($persistenceItem);

        $this->assertInstanceOf(APIItem::class, $item);
        $this->assertEquals(1, $item->getId());
        $this->assertEquals(42, $item->getCollectionId());
        $this->assertEquals(1, $item->getPosition());
        $this->assertEquals(APIItem::TYPE_OVERRIDE, $item->getType());
        $this->assertEquals('12', $item->getValueId());
        $this->assertEquals('ezcontent', $item->getValueType());
        $this->assertEquals(Value::STATUS_PUBLISHED, $item->getStatus());
        $this->assertTrue($item->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQuery()
    {
        $persistenceQuery = new Query(
            array(
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'type' => 'ezcontent_search',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'parameters' => array(
                    'en' => array(
                        'param' => 'value',
                    ),
                ),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery);

        $this->assertEquals(
            $this->queryTypeRegistry->getQueryType('ezcontent_search'),
            $query->getQueryType()
        );

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(1, $query->getId());
        $this->assertEquals(42, $query->getCollectionId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $query->getStatus());
        $this->assertTrue($query->isPublished());
        $this->assertTrue($query->isTranslatable());
        $this->assertEquals('en', $query->getMainLocale());
        $this->assertFalse($query->isAlwaysAvailable());
        $this->assertEquals(array('en'), $query->getAvailableLocales());

        $this->assertNull($query->getParameter('offset')->getValue());
        $this->assertEquals('value', $query->getParameter('param')->getValue());

        $this->assertCount(1, $query->getTranslations());
        $this->assertTrue($query->hasTranslation('en'));

        $queryTranslation = $query->getTranslation('en');

        $this->assertEquals('en', $queryTranslation->getLocale());
        $this->assertTrue($queryTranslation->isMainTranslation());

        $this->assertNull($queryTranslation->getParameter('offset')->getValue());
        $this->assertEquals('value', $queryTranslation->getParameter('param')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQueryTranslation
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getQueryLocales
     */
    public function testMapQueryWithLocales()
    {
        $persistenceQuery = new Query(
            array(
                'type' => 'ezcontent_search',
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery, array('hr'));

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(array('hr'), $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQueryTranslation
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getQueryLocales
     */
    public function testMapQueryWithAlwaysAvailableQuery()
    {
        $persistenceQuery = new Query(
            array(
                'type' => 'ezcontent_search',
                'alwaysAvailable' => true,
                'mainLocale' => 'de',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery);

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(array('en', 'hr', 'de'), $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQueryTranslation
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getQueryLocales
     */
    public function testMapQueryWithNotAlwaysAvailableQuery()
    {
        $persistenceQuery = new Query(
            array(
                'type' => 'ezcontent_search',
                'alwaysAvailable' => false,
                'mainLocale' => 'de',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery);

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(array('en', 'hr'), $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQueryTranslation
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getQueryLocales
     */
    public function testMapQueryWithLocalesAndNoContext()
    {
        $persistenceQuery = new Query(
            array(
                'type' => 'ezcontent_search',
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery, array('hr'), false);

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(array('hr'), $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQueryTranslation
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getQueryLocales
     */
    public function testMapQueryWithNoContext()
    {
        $persistenceQuery = new Query(
            array(
                'type' => 'ezcontent_search',
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery, null, false);

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(array('en', 'hr', 'de'), $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::getQueryLocales
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "42"
     */
    public function testMapQueryWithNoLocales()
    {
        $persistenceQuery = new Query(
            array(
                'id' => 42,
                'type' => 'ezcontent_search',
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $this->collectionMapper->mapQuery($persistenceQuery, array('fr'));
    }
}
