<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Registry;

use ArrayIterator;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockDefinitionRegistry::class)]
final class BlockDefinitionRegistryTest extends TestCase
{
    private BlockDefinition $blockDefinition;

    private BlockDefinitionRegistry $registry;

    protected function setUp(): void
    {
        $this->blockDefinition = new BlockDefinition();

        $this->registry = new BlockDefinitionRegistry(['block_definition' => $this->blockDefinition]);
    }

    public function testGetBlockDefinitions(): void
    {
        self::assertSame(['block_definition' => $this->blockDefinition], $this->registry->getBlockDefinitions());
    }

    public function testGetBlockDefinition(): void
    {
        self::assertSame($this->blockDefinition, $this->registry->getBlockDefinition('block_definition'));
    }

    public function testGetBlockDefinitionThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Block definition with "title" identifier does not exist.');

        $this->registry->getBlockDefinition('title');
    }

    public function testHasBlockDefinition(): void
    {
        self::assertTrue($this->registry->hasBlockDefinition('block_definition'));
    }

    public function testHasBlockDefinitionWithNoBlockDefinition(): void
    {
        self::assertFalse($this->registry->hasBlockDefinition('other_block_definition'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockDefinitions = [];
        foreach ($this->registry as $identifier => $blockDefinition) {
            $blockDefinitions[$identifier] = $blockDefinition;
        }

        self::assertSame($this->registry->getBlockDefinitions(), $blockDefinitions);
    }

    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('block_definition', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->blockDefinition, $this->registry['block_definition']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['block_definition'] = $this->blockDefinition;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['block_definition']);
    }
}
