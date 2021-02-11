<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Registry;

use ArrayIterator;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionRegistryTest extends TestCase
{
    private BlockDefinition $blockDefinition;

    private BlockDefinitionRegistry $registry;

    protected function setUp(): void
    {
        $this->blockDefinition = new BlockDefinition();

        $this->registry = new BlockDefinitionRegistry(['block_definition' => $this->blockDefinition]);
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::__construct
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::getBlockDefinitions
     */
    public function testGetBlockDefinitions(): void
    {
        self::assertSame(['block_definition' => $this->blockDefinition], $this->registry->getBlockDefinitions());
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::getBlockDefinition
     */
    public function testGetBlockDefinition(): void
    {
        self::assertSame($this->blockDefinition, $this->registry->getBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::getBlockDefinition
     */
    public function testGetBlockDefinitionThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Block definition with "title" identifier does not exist.');

        $this->registry->getBlockDefinition('title');
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinition(): void
    {
        self::assertTrue($this->registry->hasBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinitionWithNoBlockDefinition(): void
    {
        self::assertFalse($this->registry->hasBlockDefinition('other_block_definition'));
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockDefinitions = [];
        foreach ($this->registry as $identifier => $blockDefinition) {
            $blockDefinitions[$identifier] = $blockDefinition;
        }

        self::assertSame($this->registry->getBlockDefinitions(), $blockDefinitions);
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('block_definition', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->blockDefinition, $this->registry['block_definition']);
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['block_definition'] = $this->blockDefinition;
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\BlockDefinitionRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['block_definition']);
    }
}
