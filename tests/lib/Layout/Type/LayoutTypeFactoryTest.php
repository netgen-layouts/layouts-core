<?php

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\LayoutTypeFactory;
use Netgen\BlockManager\Layout\Type\Zone;
use PHPUnit\Framework\TestCase;

final class LayoutTypeFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutTypeFactory::buildLayoutType
     */
    public function testBuildLayoutType()
    {
        $layoutType = LayoutTypeFactory::buildLayoutType(
            '4_zones_a',
            array(
                'name' => '4 zones A',
                'icon' => '/icon.svg',
                'enabled' => false,
                'zones' => array(
                    'left' => array(
                        'name' => 'Left',
                        'allowed_block_definitions' => array('title', 'text'),
                    ),
                ),
            )
        );

        $this->assertEquals(
            new LayoutType(
                array(
                    'identifier' => '4_zones_a',
                    'isEnabled' => false,
                    'name' => '4 zones A',
                    'icon' => '/icon.svg',
                    'zones' => array(
                        'left' => new Zone(
                            array(
                                'identifier' => 'left',
                                'name' => 'Left',
                                'allowedBlockDefinitions' => array('title', 'text'),
                            )
                        ),
                    ),
                )
            ),
            $layoutType
        );
    }
}
