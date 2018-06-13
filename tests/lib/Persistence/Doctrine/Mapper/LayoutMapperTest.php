<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class LayoutMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new LayoutMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper::mapLayouts
     */
    public function testMapLayouts()
    {
        $data = [
            [
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My layout description',
                'created' => 123,
                'modified' => 456,
                'status' => Value::STATUS_PUBLISHED,
                'main_locale' => 'en',
                'locale' => 'en',
                'shared' => true,
            ],
            [
                'id' => 84,
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
            new Layout(
                [
                    'id' => 42,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'description' => 'My layout description',
                    'created' => 123,
                    'modified' => 456,
                    'status' => Value::STATUS_PUBLISHED,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'shared' => true,
                ]
            ),
            new Layout(
                [
                    'id' => 84,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'description' => 'My other layout description',
                    'created' => 789,
                    'modified' => 111,
                    'status' => Value::STATUS_PUBLISHED,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'shared' => false,
                ]
            ),
        ];

        $this->assertEquals($expectedData, $this->mapper->mapLayouts($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper::mapZones
     */
    public function testMapZones()
    {
        $data = [
            [
                'identifier' => 'left',
                'layout_id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'root_block_id' => 3,
                'linked_layout_id' => 3,
                'linked_zone_identifier' => 'top',
            ],
            [
                'identifier' => 'right',
                'layout_id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'root_block_id' => 4,
                'linked_layout_id' => null,
                'linked_zone_identifier' => null,
            ],
        ];

        $expectedData = [
            'left' => new Zone(
                [
                    'identifier' => 'left',
                    'layoutId' => 1,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 3,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                ]
            ),
            'right' => new Zone(
                [
                    'identifier' => 'right',
                    'layoutId' => 1,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 4,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ]
            ),
        ];

        $this->assertEquals($expectedData, $this->mapper->mapZones($data));
    }
}
