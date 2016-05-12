<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;

class LayoutMapperTest extends \PHPUnit_Framework_TestCase
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
                'parent_id' => null,
                'type' => '3_zones_a',
                'name' => 'My layout',
                'created' => 123,
                'modified' => 456,
                'status' => Layout::STATUS_PUBLISHED,
            ),
            array(
                'id' => 84,
                'parent_id' => 48,
                'type' => '3_zones_b',
                'name' => 'My other layout',
                'created' => 789,
                'modified' => 111,
                'status' => Layout::STATUS_PUBLISHED,
            ),
        );

        $expectedData = array(
            new Layout(
                array(
                    'id' => 42,
                    'parentId' => null,
                    'type' => '3_zones_a',
                    'name' => 'My layout',
                    'created' => 123,
                    'modified' => 456,
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
            new Layout(
                array(
                    'id' => 84,
                    'parentId' => 48,
                    'type' => '3_zones_b',
                    'name' => 'My other layout',
                    'created' => 789,
                    'modified' => 111,
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapLayouts($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper::mapZones
     */
    public function testMapZones()
    {
        $data = array(
            array(
                'identifier' => 'top_left',
                'layout_id' => 1,
                'status' => Layout::STATUS_PUBLISHED,
            ),
            array(
                'identifier' => 'top_right',
                'layout_id' => 1,
                'status' => Layout::STATUS_PUBLISHED,
            ),
        );

        $expectedData = array(
            new Zone(
                array(
                    'identifier' => 'top_left',
                    'layoutId' => 1,
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
            new Zone(
                array(
                    'identifier' => 'top_right',
                    'layoutId' => 1,
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapZones($data));
    }
}
