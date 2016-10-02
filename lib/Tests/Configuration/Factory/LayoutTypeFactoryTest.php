<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\Factory\LayoutTypeFactory;
use Netgen\BlockManager\Configuration\LayoutType\Zone;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
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
                '4_zones_a',
                '4 zones A',
                array(
                    'left' => new Zone(
                        'left',
                        'Left',
                        array('title', 'text')
                    ),
                )
            ),
            $layoutType
        );
    }
}
