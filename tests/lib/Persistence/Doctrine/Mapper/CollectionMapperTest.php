<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class CollectionMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new CollectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapCollections
     */
    public function testMapCollections()
    {
        $data = [
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'start' => 5,
                'length' => 10,
                'locale' => 'en',
                'translatable' => false,
                'main_locale' => 'en',
                'always_available' => true,
            ],
            [
                'id' => 43,
                'status' => Value::STATUS_DRAFT,
                'start' => 10,
                'length' => 20,
                'locale' => 'en',
                'translatable' => false,
                'main_locale' => 'en',
                'always_available' => true,
            ],
            [
                'id' => 43,
                'status' => Value::STATUS_DRAFT,
                'start' => 10,
                'length' => 20,
                'locale' => 'hr',
                'translatable' => false,
                'main_locale' => 'en',
                'always_available' => true,
            ],
        ];

        $expectedData = [
            new Collection(
                [
                    'id' => 42,
                    'status' => Value::STATUS_PUBLISHED,
                    'offset' => 5,
                    'limit' => 10,
                    'mainLocale' => 'en',
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                ]
            ),
            new Collection(
                [
                    'id' => 43,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 10,
                    'limit' => 20,
                    'mainLocale' => 'en',
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                ]
            ),
        ];

        $this->assertEquals($expectedData, $this->mapper->mapCollections($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapItems
     */
    public function testMapItems()
    {
        $data = [
            [
                'id' => 42,
                'collection_id' => 1,
                'position' => 2,
                'type' => Item::TYPE_MANUAL,
                'value' => '32',
                'value_type' => 'my_value_type',
                'status' => Value::STATUS_PUBLISHED,
                'config' => '{"config_item":{"id":42}}',
            ],
            [
                'id' => 43,
                'collection_id' => 2,
                'position' => 5,
                'type' => Item::TYPE_OVERRIDE,
                'value' => '42',
                'value_type' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => '',
            ],
        ];

        $expectedData = [
            new Item(
                [
                    'id' => 42,
                    'collectionId' => 1,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '32',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_PUBLISHED,
                    'config' => [
                        'config_item' => [
                            'id' => 42,
                        ],
                    ],
                ]
            ),
            new Item(
                [
                    'id' => 43,
                    'collectionId' => 2,
                    'position' => 5,
                    'type' => Item::TYPE_OVERRIDE,
                    'value' => '42',
                    'valueType' => 'my_value_type',
                    'status' => Value::STATUS_DRAFT,
                    'config' => [],
                ]
            ),
        ];

        $this->assertEquals($expectedData, $this->mapper->mapItems($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::buildParameters
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQuery()
    {
        $data = [
            [
                'id' => 42,
                'collection_id' => 1,
                'type' => 'my_query_type',
                'locale' => 'en',
                'parameters' => '{"param":"value"}',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 42,
                'collection_id' => 1,
                'type' => 'my_query_type',
                'locale' => 'hr',
                'parameters' => '{"param2":"value2"}',
                'status' => Value::STATUS_PUBLISHED,
            ],
        ];

        $expectedData = new Query(
            [
                'id' => 42,
                'collectionId' => 1,
                'type' => 'my_query_type',
                'availableLocales' => ['en', 'hr'],
                'parameters' => [
                    'en' => [
                        'param' => 'value',
                    ],
                    'hr' => [
                        'param2' => 'value2',
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $this->assertEquals($expectedData, $this->mapper->mapQuery($data));
    }
}
