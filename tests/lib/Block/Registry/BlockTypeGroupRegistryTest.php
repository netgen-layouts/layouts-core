<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    private $blockTypeGroup;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    private $blockTypeGroup2;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->blockTypeGroup = BlockTypeGroup::fromArray(['isEnabled' => true, 'identifier' => 'block_type_group']);
        $this->blockTypeGroup2 = BlockTypeGroup::fromArray(['isEnabled' => false, 'identifier' => 'block_type_group2']);

        $this->registry = new BlockTypeGroupRegistry(
            [
                'block_type_group' => $this->blockTypeGroup,
                'block_type_group2' => $this->blockTypeGroup2,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::__construct
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroups
     */
    public function testGetBlockTypeGroups(): void
    {
        $this->assertSame(
            [
                'block_type_group' => $this->blockTypeGroup,
                'block_type_group2' => $this->blockTypeGroup2,
            ],
            $this->registry->getBlockTypeGroups()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroups
     */
    public function testGetEnabledBlockTypeGroups(): void
    {
        $this->assertSame(
            [
                'block_type_group' => $this->blockTypeGroup,
            ],
            $this->registry->getBlockTypeGroups(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeGroup(): void
    {
        $this->assertTrue($this->registry->hasBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeWithNoBlockTypeGroup(): void
    {
        $this->assertFalse($this->registry->hasBlockTypeGroup('other_block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroup
     */
    public function testGetBlockTypeGroup(): void
    {
        $this->assertSame($this->blockTypeGroup, $this->registry->getBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroup
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockTypeException
     * @expectedExceptionMessage Block type group with "other_block_type_group" identifier does not exist.
     */
    public function testGetBlockTypeGroupThrowsBlockTypeException(): void
    {
        $this->registry->getBlockTypeGroup('other_block_type_group');
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockTypeGroups = [];
        foreach ($this->registry as $identifier => $blockTypeGroup) {
            $blockTypeGroups[$identifier] = $blockTypeGroup;
        }

        $this->assertSame($this->registry->getBlockTypeGroups(), $blockTypeGroups);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::count
     */
    public function testCount(): void
    {
        $this->assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        $this->assertArrayHasKey('block_type_group', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        $this->assertSame($this->blockTypeGroup, $this->registry['block_type_group']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['block_type_group'] = $this->blockTypeGroup;
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['block_type_group']);
    }
}
