<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Registry;

use ArrayIterator;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\Registry\BlockTypeRegistry;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockTypeRegistry::class)]
final class BlockTypeRegistryTest extends TestCase
{
    private BlockType $blockType;

    private BlockType $blockType2;

    private BlockTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->blockType = BlockType::fromArray(['isEnabled' => true, 'identifier' => 'block_type']);
        $this->blockType2 = BlockType::fromArray(['isEnabled' => false, 'identifier' => 'block_type2']);

        $this->registry = new BlockTypeRegistry(
            [
                'block_type' => $this->blockType,
                'block_type2' => $this->blockType2,
            ],
        );
    }

    public function testGetBlockTypes(): void
    {
        self::assertSame(
            [
                'block_type' => $this->blockType,
                'block_type2' => $this->blockType2,
            ],
            $this->registry->getBlockTypes(),
        );
    }

    public function testGetEnabledBlockTypes(): void
    {
        self::assertSame(
            [
                'block_type' => $this->blockType,
            ],
            $this->registry->getBlockTypes(true),
        );
    }

    public function testHasBlockType(): void
    {
        self::assertTrue($this->registry->hasBlockType('block_type'));
    }

    public function testHasBlockTypeWithNoBlockType(): void
    {
        self::assertFalse($this->registry->hasBlockType('other_block_type'));
    }

    public function testGetBlockType(): void
    {
        self::assertSame($this->blockType, $this->registry->getBlockType('block_type'));
    }

    public function testGetBlockTypeThrowsBlockTypeException(): void
    {
        $this->expectException(BlockTypeException::class);
        $this->expectExceptionMessage('Block type with "other_block_type" identifier does not exist.');

        $this->registry->getBlockType('other_block_type');
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());
        self::assertSame($this->registry->getBlockTypes(), [...$this->registry]);
    }

    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('block_type', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->blockType, $this->registry['block_type']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['block_type'] = $this->blockType;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['block_type']);
    }
}
