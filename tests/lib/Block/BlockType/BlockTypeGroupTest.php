<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockType;

use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use PHPUnit\Framework\TestCase;

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

    /**
     * @covers \Netgen\Layouts\Block\BlockType\BlockTypeGroup::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('simple_blocks', $this->blockTypeGroup->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockType\BlockTypeGroup::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertFalse($this->blockTypeGroup->isEnabled());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockType\BlockTypeGroup::getPriority
     */
    public function testGetPriority(): void
    {
        self::assertSame(42, $this->blockTypeGroup->getPriority());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockType\BlockTypeGroup::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Simple blocks', $this->blockTypeGroup->getName());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetBlockTypes(): void
    {
        self::assertSame([$this->blockType1, $this->blockType2], $this->blockTypeGroup->getBlockTypes());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetEnabledBlockTypes(): void
    {
        self::assertSame([$this->blockType1], $this->blockTypeGroup->getBlockTypes(true));
    }
}
