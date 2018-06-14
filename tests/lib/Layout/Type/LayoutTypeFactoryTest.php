<?php

declare(strict_types=1);

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
    public function testBuildLayoutType(): void
    {
        $layoutType = LayoutTypeFactory::buildLayoutType(
            '4_zones_a',
            [
                'name' => '4 zones A',
                'icon' => '/icon.svg',
                'enabled' => false,
                'zones' => [
                    'left' => [
                        'name' => 'Left',
                        'allowed_block_definitions' => ['title', 'text'],
                    ],
                ],
            ]
        );

        $this->assertEquals(
            new LayoutType(
                [
                    'identifier' => '4_zones_a',
                    'isEnabled' => false,
                    'name' => '4 zones A',
                    'icon' => '/icon.svg',
                    'zones' => [
                        'left' => new Zone(
                            [
                                'identifier' => 'left',
                                'name' => 'Left',
                                'allowedBlockDefinitions' => ['title', 'text'],
                            ]
                        ),
                    ],
                ]
            ),
            $layoutType
        );
    }
}
