<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockType;

use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockTypeGroup::class)]
final class BlockTypeGroupTest extends TestCase
{
    private BlockTypeGroup $blockTypeGroup;

    private BlockType $blockType1;

    private BlockType $blockType2;

    protected function setUp(): void
    {
        $this->blockType1 = BlockType::fromArray(['isEnabled' => true, 'identifier' => 'type']);
        $this->blockType2 = BlockType::fromArray(['isEnabled' => false, 'identifier' => 'type2']);

        $this->blockTypeGroup = BlockTypeGroup::fromArray(
            [
                'identifier' => 'simple_blocks',
                'isEnabled' => false,
                'priority' => 42,
                'name' => 'Simple blocks',
                'blockTypes' => [$this->blockType1, $this->blockType2],
            ],
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('simple_blocks', $this->blockTypeGroup->identifier);
    }

    public function testIsEnabled(): void
    {
        self::assertFalse($this->blockTypeGroup->isEnabled);
    }

    public function testGetPriority(): void
    {
        self::assertSame(42, $this->blockTypeGroup->priority);
    }

    public function testGetName(): void
    {
        self::assertSame('Simple blocks', $this->blockTypeGroup->name);
    }

    public function testGetBlockTypes(): void
    {
        self::assertSame([$this->blockType1, $this->blockType2], $this->blockTypeGroup->blockTypes);
    }

    public function testGetEnabledBlockTypes(): void
    {
        self::assertSame([$this->blockType1], $this->blockTypeGroup->enabledBlockTypes);
    }
}
