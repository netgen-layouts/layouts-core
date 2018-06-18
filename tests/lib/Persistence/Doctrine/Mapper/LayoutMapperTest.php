<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;
use PHPUnit\Framework\TestCase;

final class LayoutMapperTest extends TestCase
{
    use ExportObjectVarsTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new LayoutMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper::mapLayouts
     */
    public function testMapLayouts(): void
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
            [
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My layout description',
                'shared' => true,
                'created' => 123,
                'modified' => 456,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 84,
                'type' => '4_zones_b',
                'name' => 'My other layout',
                'description' => 'My other layout description',
                'shared' => false,
                'created' => 789,
                'modified' => 111,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
            ],
        ];

        $layouts = $this->mapper->mapLayouts($data);

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($layouts));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper::mapZones
     */
    public function testMapZones(): void
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
            'left' => [
                'identifier' => 'left',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'top',
            ],
            'right' => [
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 4,
                'linkedLayoutId' => null,
                'linkedZoneIdentifier' => null,
            ],
        ];

        $zones = $this->mapper->mapZones($data);

        foreach ($zones as $zone) {
            $this->assertInstanceOf(Zone::class, $zone);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($zones));
    }
}
