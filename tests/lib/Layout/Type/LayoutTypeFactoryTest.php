<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Layout\Type\LayoutTypeFactory;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutTypeFactory::class)]
final class LayoutTypeFactoryTest extends TestCase
{
    use ExportObjectTrait;

    public function testBuildLayoutType(): void
    {
        $layoutType = LayoutTypeFactory::buildLayoutType(
            'test_layout_1',
            [
                'name' => 'Test layout 1',
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
                'identifier' => 'test_layout_1',
                'isEnabled' => false,
                'name' => 'Test layout 1',
                'zoneIdentifiers' => ['left'],
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
