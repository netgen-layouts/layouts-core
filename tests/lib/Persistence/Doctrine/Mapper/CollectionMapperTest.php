<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;
use PHPUnit\Framework\TestCase;

final class CollectionMapperTest extends TestCase
{
    use ExportObjectVarsTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new CollectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapCollections
     */
    public function testMapCollections(): void
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
            [
                'id' => 42,
                'offset' => 5,
                'limit' => 10,
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'offset' => 10,
                'limit' => 20,
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $collections = $this->mapper->mapCollections($data);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(Collection::class, $collection);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($collections));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapItems
     */
    public function testMapItems(): void
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
            ],
            [
                'id' => 43,
                'collectionId' => 2,
                'position' => 5,
                'type' => Item::TYPE_OVERRIDE,
                'value' => '42',
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => [],
            ],
        ];

        $items = $this->mapper->mapItems($data);

        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($items));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::buildParameters
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper::mapQueries
     */
    public function testMapQueries(): void
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

        $expectedData = [
            [
                'id' => 42,
                'collectionId' => 1,
                'type' => 'my_query_type',
                'parameters' => [
                    'en' => [
                        'param' => 'value',
                    ],
                    'hr' => [
                        'param2' => 'value2',
                    ],
                ],
                'isTranslatable' => null,
                'mainLocale' => null,
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => null,
                'status' => Value::STATUS_PUBLISHED,
            ],
        ];

        $queries = $this->mapper->mapQueries($data);

        foreach ($queries as $query) {
            $this->assertInstanceOf(Query::class, $query);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($queries));
    }
}
