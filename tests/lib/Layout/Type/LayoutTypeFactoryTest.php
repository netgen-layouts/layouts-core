<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Layout\Type\LayoutTypeFactory;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutTypeFactoryTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutTypeFactory::buildLayoutType
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
            ],
        );

        self::assertTrue($layoutType->hasZone('left'));

        self::assertSame(
            [
                'icon' => '/icon.svg',
                'identifier' => '4_zones_a',
                'isEnabled' => false,
                'name' => '4 zones A',
                'zones' => [
                    'left' => [
                        'allowedBlockDefinitions' => ['title', 'text'],
                        'identifier' => 'left',
                        'name' => 'Left',
                    ],
                ],
            ],
            $this->exportObject($layoutType, true),
        );
    }
}
