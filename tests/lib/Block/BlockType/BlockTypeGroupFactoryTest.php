<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockType;

use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\BlockType\BlockTypeGroupFactory;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockTypeGroupFactory::class)]
final class BlockTypeGroupFactoryTest extends TestCase
{
    use ExportObjectTrait;

    public function testBuildBlockTypeGroup(): void
    {
        $blockType = BlockType::fromArray(['identifier' => 'title']);

        $blockTypeGroup = BlockTypeGroupFactory::buildBlockTypeGroup(
            'simple_blocks',
            [
                'enabled' => false,
                'name' => 'Simple blocks',
                'priority' => 42,
            ],
            [$blockType],
        );

        self::assertSame(
            [
                'blockTypes' => [$blockType],
                'identifier' => 'simple_blocks',
                'isEnabled' => false,
                'name' => 'Simple blocks',
                'priority' => 42,
            ],
            $this->exportObject($blockTypeGroup),
        );
    }
}
