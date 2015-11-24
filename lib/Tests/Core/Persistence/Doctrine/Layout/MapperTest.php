<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;

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
            ),
            array(
                'id' => 84,
                'parent_id' => 48,
                'identifier' => '3_zones_b',
                'name' => 'My other layout',
                'created' => 789,
                'modified' => 111,
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
            ),
            array(
                'id' => 2,
                'layout_id' => 1,
                'identifier' => 'top_right',
            ),
        );

        $expectedData = array(
            new Zone(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'identifier' => 'top_left',
                )
            ),
            new Zone(
                array(
                    'id' => 2,
                    'layoutId' => 1,
                    'identifier' => 'top_right',
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapZones($data));
    }
}
