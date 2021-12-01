<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutMapperTest extends TestCase
{
    use ExportObjectTrait;

    private LayoutMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new LayoutMapper();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutMapper::mapLayouts
     */
    public function testMapLayouts(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My layout description',
                'created' => '123',
                'modified' => '456',
                'status' => '1',
                'main_locale' => 'en',
                'locale' => 'en',
                'shared' => '1',
            ],
            [
                'id' => 84,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'type' => '4_zones_b',
                'name' => 'My other layout',
                'description' => 'My other layout description',
                'created' => 789,
                'modified' => 111,
                'status' => Value::STATUS_PUBLISHED,
                'main_locale' => 'en',
                'locale' => 'en',
                'shared' => false,
            ],
        ];

        $expectedData = [
            [
                'availableLocales' => ['en'],
                'created' => 123,
                'description' => 'My layout description',
                'id' => 42,
                'mainLocale' => 'en',
                'modified' => 456,
                'name' => 'My layout',
                'shared' => true,
                'status' => Value::STATUS_PUBLISHED,
                'type' => '4_zones_a',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            [
                'availableLocales' => ['en'],
                'created' => 789,
                'description' => 'My other layout description',
                'id' => 84,
                'mainLocale' => 'en',
                'modified' => 111,
                'name' => 'My other layout',
                'shared' => false,
                'status' => Value::STATUS_PUBLISHED,
                'type' => '4_zones_b',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
            ],
        ];

        $layouts = $this->mapper->mapLayouts($data);

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);
        self::assertSame($expectedData, $this->exportObjectList($layouts));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutMapper::mapZones
     */
    public function testMapZones(): void
    {
        $data = [
            [
                'identifier' => 'left',
                'layout_id' => '1',
                'status' => '1',
                'root_block_id' => '3',
                'linked_layout_uuid' => 'd8e55af7-cf62-5f28-ae15-331b457d82e9',
                'linked_zone_identifier' => 'top',
                'layout_uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            [
                'identifier' => 'right',
                'layout_id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'root_block_id' => 4,
                'linked_layout_uuid' => null,
                'linked_zone_identifier' => null,
                'layout_uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
        ];

        $expectedData = [
            'left' => [
                'identifier' => 'left',
                'layoutId' => 1,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'linkedLayoutUuid' => 'd8e55af7-cf62-5f28-ae15-331b457d82e9',
                'linkedZoneIdentifier' => 'top',
                'rootBlockId' => 3,
                'status' => Value::STATUS_PUBLISHED,
            ],
            'right' => [
                'identifier' => 'right',
                'layoutId' => 1,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'linkedLayoutUuid' => null,
                'linkedZoneIdentifier' => null,
                'rootBlockId' => 4,
                'status' => Value::STATUS_PUBLISHED,
            ],
        ];

        $zones = $this->mapper->mapZones($data);

        self::assertContainsOnlyInstancesOf(Zone::class, $zones);
        self::assertSame($expectedData, $this->exportObjectList($zones));
    }
}
