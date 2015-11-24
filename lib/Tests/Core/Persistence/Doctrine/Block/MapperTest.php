<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper;
use Netgen\BlockManager\Persistence\Values\Page\Block;

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
                'zone_id' => 24,
                'definition_identifier' => 'paragraph',
                'parameters' => '{"param1": "param2"}',
                'view_type' => 'default',
                'name' => 'My block',
            ),
            array(
                'id' => 84,
                'zone_id' => 48,
                'definition_identifier' => 'title',
                'parameters' => '{"param1": 42}',
                'view_type' => 'small',
                'name' => 'My other block',
            ),
        );

        $expectedData = array(
            new Block(
                array(
                    'id' => 42,
                    'zoneId' => 24,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'param1' => 'param2',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                )
            ),
            new Block(
                array(
                    'id' => 84,
                    'zoneId' => 48,
                    'definitionIdentifier' => 'title',
                    'parameters' => array(
                        'param1' => 42,
                    ),
                    'viewType' => 'small',
                    'name' => 'My other block',
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapBlocks($data));
    }
}
