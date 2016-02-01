<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper::mapBlocks
     */
    public function testMapBlocks()
    {
        $data = array(
            array(
                'id' => 42,
                'layout_id' => 24,
                'zone_identifier' => 'bottom',
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
