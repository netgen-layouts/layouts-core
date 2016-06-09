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
            '3_zones_a',
            array(
                'name' => '3 zones A',
                'enabled' => true,
                'zones' => array(
                    'left' => array(
                        'name' => 'Left',
                        'allowed_block_definitions' => array('title', 'paragraph'),
                    ),
                ),
            )
        );

        self::assertEquals(
            new LayoutType(
                '3_zones_a',
                true,
                '3 zones A',
                array(
                    'left' => new Zone(
                        'left',
                        'Left',
                        array('title', 'paragraph')
                    ),
                )
            ),
            $layoutType
        );
    }
}
