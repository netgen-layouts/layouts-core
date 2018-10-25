<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\Registry\BlockTypeRegistry;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

final class BlockTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    private $blockType;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    private $blockType2;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->blockType = BlockType::fromArray(['isEnabled' => true, 'identifier' => 'block_type']);
        $this->blockType2 = BlockType::fromArray(['isEnabled' => false, 'identifier' => 'block_type2']);

        $this->registry = new BlockTypeRegistry(
            [
                'block_type' => $this->blockType,
                'block_type2' => $this->blockType2,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypes
     */
    public function testGetBlockTypes(): void
    {
        self::assertSame(
            [
                'block_type' => $this->blockType,
                'block_type2' => $this->blockType2,
            ],
            $this->registry->getBlockTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypes
     */
    public function testGetEnabledBlockTypes(): void
    {
        self::assertSame(
            [
                'block_type' => $this->blockType,
            ],
            $this->registry->getBlockTypes(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::hasBlockType
     */
    public function testHasBlockType(): void
    {
        self::assertTrue($this->registry->hasBlockType('block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::hasBlockType
     */
    public function testHasBlockTypeWithNoBlockType(): void
    {
        self::assertFalse($this->registry->hasBlockType('other_block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockType
     */
    public function testGetBlockType(): void
    {
        self::assertSame($this->blockType, $this->registry->getBlockType('block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockType
     */
    public function testGetBlockTypeThrowsBlockTypeException(): void
    {
        $this->expectException(BlockTypeException::class);
        $this->expectExceptionMessage('Block type with "other_block_type" identifier does not exist.');

        $this->registry->getBlockType('other_block_type');
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockTypes = [];
        foreach ($this->registry as $identifier => $blockType) {
            $blockTypes[$identifier] = $blockType;
        }

        self::assertSame($this->registry->getBlockTypes(), $blockTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('block_type', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->blockType, $this->registry['block_type']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['block_type'] = $this->blockType;
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['block_type']);
    }
}
