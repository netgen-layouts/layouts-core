<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

class LayoutMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new LayoutMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper::mapLayouts
     */
    public function testMapLayouts()
    {
        $data = array(
            array(
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'created' => 123,
                'modified' => 456,
                'status' => Value::STATUS_PUBLISHED,
                'shared' => true,
            ),
            array(
                'id' => 84,
                'type' => '4_zones_b',
                'name' => 'My other layout',
                'created' => 789,
                'modified' => 111,
                'status' => Value::STATUS_PUBLISHED,
                'shared' => false,
            ),
        );

        $expectedData = array(
            new Layout(
                array(
                    'id' => 42,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'created' => 123,
                    'modified' => 456,
                    'status' => Value::STATUS_PUBLISHED,
                    'shared' => true,
                )
            ),
            new Layout(
                array(
                    'id' => 84,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'created' => 789,
                    'modified' => 111,
                    'status' => Value::STATUS_PUBLISHED,
                    'shared' => false,
                )
            ),
        );

        $this->assertEquals($expectedData, $this->mapper->mapLayouts($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper::mapZones
     */
    public function testMapZones()
    {
        $data = array(
            array(
                'identifier' => 'left',
                'layout_id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'root_block_id' => 3,
                'linked_layout_id' => 3,
                'linked_zone_identifier' => 'top',
            ),
            array(
                'identifier' => 'right',
                'layout_id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'root_block_id' => 4,
                'linked_layout_id' => null,
                'linked_zone_identifier' => null,
            ),
        );

        $expectedData = array(
            new Zone(
                array(
                    'identifier' => 'left',
                    'layoutId' => 1,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 3,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                )
            ),
            new Zone(
                array(
                    'identifier' => 'right',
                    'layoutId' => 1,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 4,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                )
            ),
        );

        $this->assertEquals($expectedData, $this->mapper->mapZones($data));
    }
}
