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
            $this->exportObject($layoutType, true),
        );
    }
}
