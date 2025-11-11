<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockType;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockType\BlockType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockType::class)]
final class BlockTypeTest extends TestCase
{
    private BlockType $blockType;

    private BlockDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(['identifier' => 'title']);

        $this->blockType = BlockType::fromArray(
            [
                'identifier' => 'title',
                'name' => 'Title',
                'icon' => '/icon.svg',
                'isEnabled' => false,
                'definition' => $this->blockDefinition,
                'defaults' => [
                    'name' => 'Name',
                    'view_type' => 'default',
                    'item_view_type' => 'standard',
                    'parameters' => ['tag' => 'h3'],
                ],
            ],
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('title', $this->blockType->getIdentifier());
    }

    public function testIsEnabled(): void
    {
        self::assertFalse($this->blockType->isEnabled());
    }

    public function testGetName(): void
    {
        self::assertSame('Title', $this->blockType->getName());
    }

    public function testGetIcon(): void
    {
        self::assertSame('/icon.svg', $this->blockType->getIcon());
    }

    public function testGetDefinition(): void
    {
        self::assertSame($this->blockDefinition, $this->blockType->getDefinition());
    }

    public function testGetDefaults(): void
    {
        self::assertSame(
            [
                'name' => 'Name',
                'view_type' => 'default',
                'item_view_type' => 'standard',
                'parameters' => ['tag' => 'h3'],
            ],
            $this->blockType->getDefaults(),
        );
    }

    public function testGetDefaultName(): void
    {
        self::assertSame('Name', $this->blockType->getDefaultName());
    }

    public function testGetDefaultViewType(): void
    {
        self::assertSame('default', $this->blockType->getDefaultViewType());
    }

    public function testGetDefaultItemViewType(): void
    {
        self::assertSame('standard', $this->blockType->getDefaultItemViewType());
    }

    public function testGetDefaultParameters(): void
    {
        self::assertSame(['tag' => 'h3'], $this->blockType->getDefaultParameters());
    }

    public function testGetDefaultEmptyName(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->getDefaultName());
    }

    public function testGetDefaultEmptyViewType(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->getDefaultViewType());
    }

    public function testGetDefaultEmptyItemViewType(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->getDefaultItemViewType());
    }

    public function testGetDefaultEmptyParameters(): void
    {
        $this->blockType = new BlockType();

        self::assertSame([], $this->blockType->getDefaultParameters());
    }
}
