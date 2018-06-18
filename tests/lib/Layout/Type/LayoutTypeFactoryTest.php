<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\LayoutTypeFactory;
use Netgen\BlockManager\Layout\Type\Zone;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;
use PHPUnit\Framework\TestCase;

final class LayoutTypeFactoryTest extends TestCase
{
    use ExportObjectVarsTrait;

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

        $this->assertInstanceOf(LayoutType::class, $layoutType);

        $this->assertArrayHasKey('left', $layoutType->getZones());
        $this->assertInstanceOf(Zone::class, $layoutType->getZone('left'));

        $this->assertSame(
            [
                'identifier' => '4_zones_a',
                'isEnabled' => false,
                'name' => '4 zones A',
                'icon' => '/icon.svg',
                'zones' => [
                    'left' => [
                        'identifier' => 'left',
                        'name' => 'Left',
                        'allowedBlockDefinitions' => ['title', 'text'],
                    ],
                ],
            ],
            $this->exportObjectVars($layoutType, true)
        );
    }
}
