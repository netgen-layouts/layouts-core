<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;

abstract class CollectionMapperTest extends MapperTest
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
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
                'status' => APICollection::STATUS_PUBLISHED,
                'type' => APICollection::TYPE_DYNAMIC,
                'name' => null,
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        self::assertInstanceOf(APICollection::class, $collection);
        self::assertEquals(2, $collection->getId());
        self::assertEquals(APICollection::TYPE_DYNAMIC, $collection->getType());
        self::assertNull(null, $collection->getName());
        self::assertEquals(APICollection::STATUS_PUBLISHED, $collection->getStatus());

        foreach ($collection->getItems() as $item) {
            self::assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getManualItems() as $item) {
            self::assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getOverrideItems() as $item) {
            self::assertInstanceOf(APIItem::class, $item);
        }

        self::assertEquals(
            count($collection->getItems()),
            count($collection->getManualItems()) + count($collection->getOverrideItems())
        );

        foreach ($collection->getQueries() as $query) {
            self::assertInstanceOf(APIQuery::class, $query);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapItem
     */
    public function testMapItem()
    {
        $persistenceItem = new Item(
            array(
                'id' => 1,
                'status' => APICollection::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
                'type' => APIItem::TYPE_OVERRIDE,
                'valueId' => '12',
                'valueType' => 'ezcontent',
            )
        );

        $item = $this->collectionMapper->mapItem($persistenceItem);

        self::assertInstanceOf(APIItem::class, $item);
        self::assertEquals(1, $item->getId());
        self::assertEquals(42, $item->getCollectionId());
        self::assertEquals(1, $item->getPosition());
        self::assertEquals(APIItem::TYPE_OVERRIDE, $item->getType());
        self::assertEquals('12', $item->getValueId());
        self::assertEquals('ezcontent', $item->getValueType());
        self::assertEquals(APICollection::STATUS_PUBLISHED, $item->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQuery()
    {
        $persistenceQuery = new Query(
            array(
                'id' => 1,
                'status' => APICollection::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
                'identifier' => 'my_search',
                'type' => 'ezcontent_search',
                'parameters' => array('param' => 'value'),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery);

        self::assertInstanceOf(APIQuery::class, $query);
        self::assertEquals(1, $query->getId());
        self::assertEquals(42, $query->getCollectionId());
        self::assertEquals(1, $query->getPosition());
        self::assertEquals('my_search', $query->getIdentifier());
        self::assertEquals('ezcontent_search', $query->getType());
        self::assertEquals(array('param' => 'value'), $query->getParameters());
        self::assertEquals(APICollection::STATUS_PUBLISHED, $query->getStatus());
    }
}
