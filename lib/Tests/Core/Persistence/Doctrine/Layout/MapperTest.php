<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Block;
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
                'identifier' => 'top_left',
                'layout_id' => 1,
                'status' => APILayout::STATUS_PUBLISHED,
            ),
            array(
                'identifier' => 'top_right',
                'layout_id' => 1,
                'status' => APILayout::STATUS_PUBLISHED,
            ),
        );

        $expectedData = array(
            new Zone(
                array(
                    'identifier' => 'top_left',
                    'layoutId' => 1,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            new Zone(
                array(
                    'identifier' => 'top_right',
                    'layoutId' => 1,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapZones($data));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper::mapBlocks
     */
    public function testMapBlocks()
    {
        $data = array(
            array(
                'id' => 42,
                'layout_id' => 24,
                'zone_identifier' => 'bottom',
                'position' => 4,
                'definition_identifier' => 'paragraph',
                'parameters' => '{"param1": "param2"}',
                'view_type' => 'default',
                'name' => 'My block',
                'status' => APILayout::STATUS_PUBLISHED,
            ),
            array(
                'id' => 84,
                'layout_id' => 48,
                'zone_identifier' => 'top',
                'position' => 3,
                'definition_identifier' => 'title',
                'parameters' => '{"param1": 42}',
                'view_type' => 'small',
                'name' => 'My other block',
                'status' => APILayout::STATUS_PUBLISHED,
            ),
        );

        $expectedData = array(
            new Block(
                array(
                    'id' => 42,
                    'layoutId' => 24,
                    'zoneIdentifier' => 'bottom',
                    'position' => 4,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'param1' => 'param2',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            new Block(
                array(
                    'id' => 84,
                    'layoutId' => 48,
                    'zoneIdentifier' => 'top',
                    'position' => 3,
                    'definitionIdentifier' => 'title',
                    'parameters' => array(
                        'param1' => 42,
                    ),
                    'viewType' => 'small',
                    'name' => 'My other block',
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapBlocks($data));
    }
}
