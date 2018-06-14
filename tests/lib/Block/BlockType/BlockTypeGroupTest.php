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

    public function setUp(): void
    {
        $this->blockTypeGroup = new BlockTypeGroup(
            [
                'identifier' => 'simple_blocks',
                'isEnabled' => false,
                'name' => 'Simple blocks',
                'blockTypes' => [
                    new BlockType(['isEnabled' => true, 'identifier' => 'type']),
                    new BlockType(['isEnabled' => false, 'identifier' => 'type2']),
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::__construct
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('simple_blocks', $this->blockTypeGroup->getIdentifier());
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
        $this->assertEquals('Simple blocks', $this->blockTypeGroup->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetBlockTypes(): void
    {
        $this->assertEquals(
            [
                new BlockType(['isEnabled' => true, 'identifier' => 'type']),
                new BlockType(['isEnabled' => false, 'identifier' => 'type2']),
            ],
            $this->blockTypeGroup->getBlockTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetEnabledBlockTypes(): void
    {
        $this->assertEquals(
            [
                new BlockType(['isEnabled' => true, 'identifier' => 'type']),
            ],
            $this->blockTypeGroup->getBlockTypes(true)
        );
    }
}
