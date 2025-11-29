<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Registry;

use ArrayIterator;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Netgen\Layouts\Block\Registry\BlockTypeGroupRegistry;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockTypeGroupRegistry::class)]
final class BlockTypeGroupRegistryTest extends TestCase
{
    private BlockTypeGroup $blockTypeGroup;

    private BlockTypeGroup $blockTypeGroup2;

    private BlockTypeGroupRegistry $registry;

    protected function setUp(): void
    {
        $this->blockTypeGroup = BlockTypeGroup::fromArray(['isEnabled' => true, 'identifier' => 'block_type_group']);
        $this->blockTypeGroup2 = BlockTypeGroup::fromArray(['isEnabled' => false, 'identifier' => 'block_type_group2']);

        $this->registry = new BlockTypeGroupRegistry(
            [
                'block_type_group' => $this->blockTypeGroup,
                'block_type_group2' => $this->blockTypeGroup2,
            ],
        );
    }

    public function testGetBlockTypeGroups(): void
    {
        self::assertSame(
            [
                'block_type_group' => $this->blockTypeGroup,
                'block_type_group2' => $this->blockTypeGroup2,
            ],
            $this->registry->getBlockTypeGroups(),
        );
    }

    public function testGetEnabledBlockTypeGroups(): void
    {
        self::assertSame(
            [
                'block_type_group' => $this->blockTypeGroup,
            ],
            $this->registry->getBlockTypeGroups(true),
        );
    }

    public function testHasBlockTypeGroup(): void
    {
        self::assertTrue($this->registry->hasBlockTypeGroup('block_type_group'));
    }

    public function testHasBlockTypeWithNoBlockTypeGroup(): void
    {
        self::assertFalse($this->registry->hasBlockTypeGroup('other_block_type_group'));
    }

    public function testGetBlockTypeGroup(): void
    {
        self::assertSame($this->blockTypeGroup, $this->registry->getBlockTypeGroup('block_type_group'));
    }

    public function testGetBlockTypeGroupThrowsBlockTypeException(): void
    {
        $this->expectException(BlockTypeException::class);
        $this->expectExceptionMessage('Block type group with "other_block_type_group" identifier does not exist.');

        $this->registry->getBlockTypeGroup('other_block_type_group');
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());
        self::assertSame($this->registry->getBlockTypeGroups(), [...$this->registry]);
    }

    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('block_type_group', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->blockTypeGroup, $this->registry['block_type_group']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['block_type_group'] = $this->blockTypeGroup;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['block_type_group']);
    }
}
