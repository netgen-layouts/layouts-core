<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $blockDefinition;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new BlockDefinitionRegistry();

        $this->blockDefinition = new BlockDefinition();

        $this->registry->addBlockDefinition('block_definition', $this->blockDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::addBlockDefinition
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getBlockDefinitions
     */
    public function testAddBlockDefinition()
    {
        $this->assertEquals(['block_definition' => $this->blockDefinition], $this->registry->getBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getBlockDefinition
     */
    public function testGetBlockDefinition()
    {
        $this->assertEquals($this->blockDefinition, $this->registry->getBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getBlockDefinition
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Block definition with "title" identifier does not exist.
     */
    public function testGetBlockDefinitionThrowsBlockDefinitionException()
    {
        $this->registry->getBlockDefinition('title');
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinition()
    {
        $this->assertTrue($this->registry->hasBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinitionWithNoBlockDefinition()
    {
        $this->assertFalse($this->registry->hasBlockDefinition('other_block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockDefinitions = [];
        foreach ($this->registry as $identifier => $blockDefinition) {
            $blockDefinitions[$identifier] = $blockDefinition;
        }

        $this->assertEquals($this->registry->getBlockDefinitions(), $blockDefinitions);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::count
     */
    public function testCount()
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('block_definition', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals($this->blockDefinition, $this->registry['block_definition']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet()
    {
        $this->registry['block_definition'] = $this->blockDefinition;
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset()
    {
        unset($this->registry['block_definition']);
    }
}
