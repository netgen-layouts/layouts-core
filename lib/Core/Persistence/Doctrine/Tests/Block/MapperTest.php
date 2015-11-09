<?php

namespace Netgen\BlockManager\Core\Persistence\Tests\Doctrine\Block;

use Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use PHPUnit_Framework_TestCase;

class MapperTest extends PHPUnit_Framework_TestCase
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
            ),
            array(
                'id' => 84,
                'zone_id' => 48,
                'definition_identifier' => 'title',
                'parameters' => '{"param1": 42}',
                'view_type' => 'small',
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
                )
            ),
        );

        $mapper = new Mapper();
        self::assertEquals($expectedData, $mapper->mapBlocks($data));
    }
}
