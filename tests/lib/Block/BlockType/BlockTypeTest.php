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
        self::assertSame('title', $this->blockType->identifier);
    }

    public function testIsEnabled(): void
    {
        self::assertFalse($this->blockType->isEnabled);
    }

    public function testGetName(): void
    {
        self::assertSame('Title', $this->blockType->name);
    }

    public function testGetIcon(): void
    {
        self::assertSame('/icon.svg', $this->blockType->icon);
    }

    public function testGetDefinition(): void
    {
        self::assertSame($this->blockDefinition, $this->blockType->definition);
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
            $this->blockType->defaults,
        );
    }

    public function testGetDefaultName(): void
    {
        self::assertSame('Name', $this->blockType->defaultName);
    }

    public function testGetDefaultViewType(): void
    {
        self::assertSame('default', $this->blockType->defaultViewType);
    }

    public function testGetDefaultItemViewType(): void
    {
        self::assertSame('standard', $this->blockType->defaultItemViewType);
    }

    public function testGetDefaultParameters(): void
    {
        self::assertSame(['tag' => 'h3'], $this->blockType->defaultParameters);
    }

    public function testGetDefaultEmptyName(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->defaultName);
    }

    public function testGetDefaultEmptyViewType(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->defaultViewType);
    }

    public function testGetDefaultEmptyItemViewType(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->defaultItemViewType);
    }

    public function testGetDefaultEmptyParameters(): void
    {
        $this->blockType = new BlockType();

        self::assertSame([], $this->blockType->defaultParameters);
    }
}
