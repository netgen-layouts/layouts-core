<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\Factory\LayoutTypeFactory;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone;
use PHPUnit\Framework\TestCase;

class LayoutTypeFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\Factory\LayoutTypeFactory::buildLayoutType
     */
    public function testBuildLayoutType()
    {
        $layoutType = LayoutTypeFactory::buildLayoutType(
            '4_zones_a',
            array(
                'name' => '4 zones A',
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
