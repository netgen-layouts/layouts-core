<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockType;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockType\BlockTypeFactory;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class BlockTypeFactoryTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\Layouts\Block\BlockType\BlockTypeFactory::buildBlockType
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
            $blockDefinition,
        );

        self::assertSame(
            [
                'defaults' => [
                    'viewType' => 'default',
                ],
                'definition' => $blockDefinition,
                'icon' => '/icon.svg',
                'identifier' => 'title',
                'isEnabled' => false,
                'name' => 'Title',
            ],
            $this->exportObject($blockType),
        );
    }
}
