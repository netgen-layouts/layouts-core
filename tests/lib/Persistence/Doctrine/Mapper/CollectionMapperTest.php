<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class CollectionMapperTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper
     */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new CollectionMapper();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper::mapCollections
     */
    public function testMapCollections(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'status' => '1',
                'start' => '5',
                'length' => '10',
                'locale' => 'en',
                'translatable' => '0',
                'main_locale' => 'en',
                'always_available' => '1',
            ],
            [
                'id' => 43,
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
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
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
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
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
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
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
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

        self::assertContainsOnlyInstancesOf(Collection::class, $collections);
        self::assertSame($expectedData, $this->exportObjectList($collections));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper::mapItems
     */
    public function testMapItems(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'collection_id' => '1',
                'collection_uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'position' => '2',
                'value' => '32',
                'value_type' => 'my_value_type',
                'status' => '1',
                'config' => '{"config_item":{"id":42}}',
            ],
            [
                'id' => 43,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collection_id' => 2,
                'collection_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'position' => 5,
                'value' => '42',
                'value_type' => 'my_value_type',
                'status' => Value::STATUS_DRAFT,
                'config' => '',
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'collectionId' => 1,
                'collectionUuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'position' => 2,
                'value' => '32',
                'valueType' => 'my_value_type',
                'config' => [
                    'config_item' => [
                        'id' => 42,
                    ],
                ],
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionId' => 2,
                'collectionUuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'position' => 5,
                'value' => '42',
                'valueType' => 'my_value_type',
                'config' => [],
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $items = $this->mapper->mapItems($data);

        self::assertContainsOnlyInstancesOf(Item::class, $items);
        self::assertSame($expectedData, $this->exportObjectList($items));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper::buildParameters
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper::mapQueries
     */
    public function testMapQueries(): void
    {
        $data = [
            [
                'id' => '43',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'collection_id' => '1',
                'collection_uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'my_query_type',
                'locale' => 'en',
                'parameters' => '{"param":"value"}',
                'status' => '1',
            ],
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collection_id' => 1,
                'collection_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'my_query_type',
                'locale' => 'en',
                'parameters' => '{"param":"value"}',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collection_id' => 1,
                'collection_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'my_query_type',
                'locale' => 'hr',
                'parameters' => '{"param2":"value2"}',
                'status' => Value::STATUS_PUBLISHED,
            ],
        ];

        $expectedData = [
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'collectionId' => 1,
                'collectionUuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'my_query_type',
                'parameters' => [
                    'en' => [
                        'param' => 'value',
                    ],
                ],
                'isTranslatable' => null,
                'mainLocale' => null,
                'availableLocales' => ['en'],
                'alwaysAvailable' => null,
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionId' => 1,
                'collectionUuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
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

        self::assertContainsOnlyInstancesOf(Query::class, $queries);
        self::assertSame($expectedData, $this->exportObjectList($queries));
    }
}
