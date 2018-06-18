<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    private $blockTypeGroup;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    private $blockType1;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    private $blockType2;

    public function setUp(): void
    {
        $this->blockType1 = new BlockType(['isEnabled' => true, 'identifier' => 'type']);
        $this->blockType2 = new BlockType(['isEnabled' => false, 'identifier' => 'type2']);

        $this->blockTypeGroup = new BlockTypeGroup(
            [
                'identifier' => 'simple_blocks',
                'isEnabled' => false,
                'name' => 'Simple blocks',
                'blockTypes' => [$this->blockType1, $this->blockType2],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::__construct
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('simple_blocks', $this->blockTypeGroup->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->assertFalse($this->blockTypeGroup->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Simple blocks', $this->blockTypeGroup->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetBlockTypes(): void
    {
        $this->assertSame([$this->blockType1, $this->blockType2], $this->blockTypeGroup->getBlockTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetEnabledBlockTypes(): void
    {
        $this->assertSame([$this->blockType1], $this->blockTypeGroup->getBlockTypes(true));
    }
}
