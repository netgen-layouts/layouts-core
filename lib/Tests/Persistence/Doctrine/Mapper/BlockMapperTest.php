<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;

class BlockMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new BlockMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper::mapBlocks
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

        self::assertEquals($expectedData, $this->mapper->mapBlocks($data));
    }
}
