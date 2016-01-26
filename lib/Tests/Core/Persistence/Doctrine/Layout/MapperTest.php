<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper::mapLayouts
     */
    public function testMapLayouts()
    {
        $data = array(
            array(
                'id' => 42,
                'parent_id' => null,
                'identifier' => '3_zones_a',
                'name' => 'My layout',
                'created' => 123,
                'modified' => 456,
                'status' => APILayout::STATUS_PUBLISHED,
            ),
            array(
                'id' => 84,
                'parent_id' => 48,
                'identifier' => '3_zones_b',
                'name' => 'My other layout',
                'created' => 789,
                'modified' => 111,
                'status' => APILayout::STATUS_PUBLISHED,
            ),
        );

        $expectedData = array(
            new Layout(
                array(
                    'id' => 42,
                    'parentId' => null,
                    'identifier' => '3_zones_a',
                    'name' => 'My layout',
                    'created' => 123,
                    'modified' => 456,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            new Layout(
                array(
                    'id' => 84,
                    'parentId' => 48,
                    'identifier' => '3_zones_b',
                    'name' => 'My other layout',
                    'created' => 789,
                    'modified' => 111,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapLayouts($data));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper::mapZones
     */
    public function testMapZones()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 1,
                'identifier' => 'top_left',
                'status' => APILayout::STATUS_PUBLISHED,
            ),
            array(
                'id' => 2,
                'layout_id' => 1,
                'identifier' => 'top_right',
                'status' => APILayout::STATUS_PUBLISHED,
            ),
        );

        $expectedData = array(
            new Zone(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'identifier' => 'top_left',
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            new Zone(
                array(
                    'id' => 2,
                    'layoutId' => 1,
                    'identifier' => 'top_right',
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapZones($data));
    }
}
