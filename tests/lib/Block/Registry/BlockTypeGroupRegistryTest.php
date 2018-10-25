<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
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
        self::assertSame(
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
        self::assertSame(
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
        self::assertTrue($this->registry->hasBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeWithNoBlockTypeGroup(): void
    {
        self::assertFalse($this->registry->hasBlockTypeGroup('other_block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroup
     */
    public function testGetBlockTypeGroup(): void
    {
        self::assertSame($this->blockTypeGroup, $this->registry->getBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroup
     */
    public function testGetBlockTypeGroupThrowsBlockTypeException(): void
    {
        $this->expectException(BlockTypeException::class);
        $this->expectExceptionMessage('Block type group with "other_block_type_group" identifier does not exist.');

        $this->registry->getBlockTypeGroup('other_block_type_group');
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockTypeGroups = [];
        foreach ($this->registry as $identifier => $blockTypeGroup) {
            $blockTypeGroups[$identifier] = $blockTypeGroup;
        }

        self::assertSame($this->registry->getBlockTypeGroups(), $blockTypeGroups);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('block_type_group', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->blockTypeGroup, $this->registry['block_type_group']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['block_type_group'] = $this->blockTypeGroup;
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['block_type_group']);
    }
}
