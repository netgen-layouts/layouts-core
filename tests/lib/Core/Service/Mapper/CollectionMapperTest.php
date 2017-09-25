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
     */
    public function testMapCollectionWithLocale()
    {
        $persistenceCollection = new Collection(
            array(
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, array('hr'));

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('en', 'hr', 'de'), $collection->getAvailableLocales());
        $this->assertEquals('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocales()
    {
        $persistenceCollection = new Collection(
            array(
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, array('hr', 'en'));

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('en', 'hr', 'de'), $collection->getAvailableLocales());
        $this->assertEquals('hr', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollectionWithLocalesAndAlwaysAvailable()
    {
        $persistenceCollection = new Collection(
            array(
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection, array('fr', 'no'));

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(array('en', 'hr', 'de'), $collection->getAvailableLocales());
        $this->assertEquals('en', $collection->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "42"
     */
    public function testMapCollectionWithLocalesAndAlwaysAvailableWithoutUsingMainLocale()
    {
        $persistenceCollection = new Collection(
            array(
                'id' => 42,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $this->collectionMapper->mapCollection($persistenceCollection, array('fr', 'no'), false);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "42"
     */
    public function testMapCollectionWithLocalesAndNotAlwaysAvailable()
    {
        $persistenceCollection = new Collection(
            array(
                'id' => 42,
                'mainLocale' => 'en',
                'alwaysAvailable' => false,
                'availableLocales' => array('en', 'hr', 'de'),
            )
        );

        $this->collectionMapper->mapCollection($persistenceCollection, array('fr', 'no'));
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

        $this->assertEquals('en', $query->getLocale());

        $this->assertNull($query->getParameter('offset')->getValue());
        $this->assertEquals('value', $query->getParameter('param')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocale()
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
        $this->assertEquals(array('en', 'hr', 'de'), $query->getAvailableLocales());
        $this->assertEquals('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
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

        $query = $this->collectionMapper->mapQuery($persistenceQuery, array('hr', 'en'));

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(array('en', 'hr', 'de'), $query->getAvailableLocales());
        $this->assertEquals('hr', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQueryWithLocalesAndAlwaysAvailable()
    {
        $persistenceQuery = new Query(
            array(
                'type' => 'ezcontent_search',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery, array('fr', 'no'));

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(array('en', 'hr', 'de'), $query->getAvailableLocales());
        $this->assertEquals('en', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "42"
     */
    public function testMapQueryWithLocalesAndAlwaysAvailableWithoutUsingMainLocale()
    {
        $persistenceQuery = new Query(
            array(
                'id' => 42,
                'type' => 'ezcontent_search',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $this->collectionMapper->mapQuery($persistenceQuery, array('fr', 'no'), false);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "42"
     */
    public function testMapQueryWithLocalesAndNotAlwaysAvailable()
    {
        $persistenceQuery = new Query(
            array(
                'id' => 42,
                'type' => 'ezcontent_search',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
            )
        );

        $this->collectionMapper->mapQuery($persistenceQuery, array('fr', 'no'));
    }
}
