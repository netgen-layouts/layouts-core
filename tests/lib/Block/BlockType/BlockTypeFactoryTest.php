<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeFactory;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;
use PHPUnit\Framework\TestCase;

final class BlockTypeFactoryTest extends TestCase
{
    use ExportObjectVarsTrait;

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeFactory::buildBlockType
     */
    public function testBuildBlockType(): void
    {
        $blockDefinition = new BlockDefinition();

        $blockType = BlockTypeFactory::buildBlockType(
            'title',
            [
                'name' => 'Title',
                'icon' => '/icon.svg',
                'enabled' => false,
                'definition_identifier' => 'title',
                'defaults' => [
                    'viewType' => 'default',
                ],
            ],
            $blockDefinition
        );

        $this->assertInstanceOf(BlockType::class, $blockType);

        $this->assertSame(
            [
                'identifier' => 'title',
                'isEnabled' => false,
                'name' => 'Title',
                'icon' => '/icon.svg',
                'definition' => $blockDefinition,
                'defaults' => [
                    'viewType' => 'default',
                ],
            ],
            $this->exportObjectVars($blockType)
        );
    }
}
