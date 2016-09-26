<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

class CollectionMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new CollectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapCollections
     */
    public function testMapCollections()
    {
        $data = array(
            array(
                'id' => 42,
                'type' => Collection::TYPE_DYNAMIC,
                'shared' => true,
                'name' => 'My collection',
                'status' => Collection::STATUS_PUBLISHED,
            ),
            array(
                'id' => 43,
                'type' => Collection::TYPE_MANUAL,
                'shared' => false,
                'name' => null,
                'status' => Collection::STATUS_DRAFT,
            ),
        );

        $expectedData = array(
            new Collection(
                array(
                    'id' => 42,
                    'type' => Collection::TYPE_DYNAMIC,
                    'shared' => true,
                    'name' => 'My collection',
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            new Collection(
                array(
                    'id' => 43,
                    'type' => Collection::TYPE_MANUAL,
                    'shared' => false,
                    'name' => null,
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
        );

        $this->assertEquals($expectedData, $this->mapper->mapCollections($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapItems
     */
    public function testMapItems()
    {
        $data = array(
            array(
                'id' => 42,
                'collection_id' => 1,
                'position' => 2,
                'type' => Item::TYPE_MANUAL,
                'value_id' => '32',
                'value_type' => 'ezcontent',
                'status' => Collection::STATUS_PUBLISHED,
            ),
            array(
                'id' => 43,
                'collection_id' => 2,
                'position' => 5,
                'type' => Item::TYPE_OVERRIDE,
                'value_id' => '42',
                'value_type' => 'ezcontent',
                'status' => Collection::STATUS_DRAFT,
            ),
        );

        $expectedData = array(
            new Item(
                array(
                    'id' => 42,
                    'collectionId' => 1,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => '32',
                    'valueType' => 'ezcontent',
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            new Item(
                array(
                    'id' => 43,
                    'collectionId' => 2,
                    'position' => 5,
                    'type' => Item::TYPE_OVERRIDE,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
        );

        $this->assertEquals($expectedData, $this->mapper->mapItems($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapQueries
     */
    public function testMapQueries()
    {
        $data = array(
            array(
                'id' => 42,
                'collection_id' => 1,
                'position' => 2,
                'identifier' => 'default',
                'type' => 'ezcontent_search',
                'parameters' => '{"param":"value"}',
                'status' => Collection::STATUS_PUBLISHED,
            ),
            array(
                'id' => 43,
                'collection_id' => 2,
                'position' => 5,
                'identifier' => 'featured',
                'type' => 'ezcontent_search',
                'parameters' => '{"param2":"value2"}',
                'status' => Collection::STATUS_DRAFT,
            ),
        );

        $expectedData = array(
            new Query(
                array(
                    'id' => 42,
                    'collectionId' => 1,
                    'position' => 2,
                    'identifier' => 'default',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                    'status' => Collection::STATUS_PUBLISHED,
                )
            ),
            new Query(
                array(
                    'id' => 43,
                    'collectionId' => 2,
                    'position' => 5,
                    'identifier' => 'featured',
                    'type' => 'ezcontent_search',
                    'parameters' => array(
                        'param2' => 'value2',
                    ),
                    'status' => Collection::STATUS_DRAFT,
                )
            ),
        );

        $this->assertEquals($expectedData, $this->mapper->mapQueries($data));
    }
}
