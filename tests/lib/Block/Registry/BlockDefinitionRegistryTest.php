<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use PHPUnit\Framework\TestCase;

class BlockDefinitionRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockDefinitionRegistry();

        $this->blockDefinition = new BlockDefinition('block_definition');

        $this->registry->addBlockDefinition('block_definition', $this->blockDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::addBlockDefinition
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getBlockDefinitions
     */
    public function testAddBlockDefinition()
    {
        $this->assertEquals(array('block_definition' => $this->blockDefinition), $this->registry->getBlockDefinitions());
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
}
