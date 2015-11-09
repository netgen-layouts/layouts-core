<?php

namespace Netgen\BlockManager\Core\Persistence\Tests\Doctrine\Layout;

use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use PHPUnit_Framework_TestCase;

class MapperTest extends PHPUnit_Framework_TestCase
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
                'created' => 123,
                'modified' => 456,
            ),
            array(
                'id' => 84,
                'parent_id' => 48,
                'identifier' => '3_zones_b',
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
                    'created' => 123,
                    'modified' => 456,
                )
            ),
            new Layout(
                array(
                    'id' => 84,
                    'parentId' => 48,
                    'identifier' => '3_zones_b',
                    'created' => 789,
                    'modified' => 111,
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapLayouts($data));
    }
}
