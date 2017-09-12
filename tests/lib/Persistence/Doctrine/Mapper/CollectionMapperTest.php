<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Value;
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
                'status' => Value::STATUS_PUBLISHED,
                'locale' => 'en',
                'translatable' => false,
                'main_locale' => 'en',
                'always_available' => true,
            ),
            array(
                'id' => 43,
                'status' => Value::STATUS_DRAFT,
                'locale' => 'en',
                'translatable' => false,
                'main_locale' => 'en',
                'always_available' => true,
            ),
            array(
                'id' => 43,
                'status' => Value::STATUS_DRAFT,
                'locale' => 'hr',
                'translatable' => false,
                'main_locale' => 'en',
                'always_available' => true,
            ),
        );

        $expectedData = array(
            new Collection(
                array(
                    'id' => 42,
                    'status' => Value::STATUS_PUBLISHED,
                    'mainLocale' => 'en',
                    'alwaysAvailable' => true,
                    'availableLocales' => array('en'),
                )
            ),
            new Collection(
                array(
                    'id' => 43,
                    'status' => Value::STATUS_DRAFT,
                    'mainLocale' => 'en',
                    'alwaysAvailable' => true,
                    'availableLocales' => array('en', 'hr'),
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
                'status' => Value::STATUS_PUBLISHED,
            ),
            array(
                'id' => 43,
                'collection_id' => 2,
                'position' => 5,
                'type' => Item::TYPE_OVERRIDE,
                'value_id' => '42',
                'value_type' => 'ezcontent',
                'status' => Value::STATUS_DRAFT,
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
                    'status' => Value::STATUS_PUBLISHED,
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
                    'status' => Value::STATUS_DRAFT,
                )
            ),
        );

        $this->assertEquals($expectedData, $this->mapper->mapItems($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::buildParameters
     */
    public function testMapQuery()
    {
        $data = array(
            array(
                'id' => 42,
                'collection_id' => 1,
                'type' => 'ezcontent_search',
                'locale' => 'en',
                'parameters' => '{"param":"value"}',
                'status' => Value::STATUS_PUBLISHED,
            ),
            array(
                'id' => 42,
                'collection_id' => 1,
                'type' => 'ezcontent_search',
                'locale' => 'hr',
                'parameters' => '{"param2":"value2"}',
                'status' => Value::STATUS_PUBLISHED,
            ),
        );

        $expectedData = new Query(
            array(
                'id' => 42,
                'collectionId' => 1,
                'type' => 'ezcontent_search',
                'availableLocales' => array('en', 'hr'),
                'parameters' => array(
                    'en' => array(
                        'param' => 'value',
                    ),
                    'hr' => array(
                        'param2' => 'value2',
                    ),
                ),
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals($expectedData, $this->mapper->mapQuery($data));
    }
}
