<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Registry;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry;

class BlockDefinitionRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface
     */
    protected $blockDefinition;

    /**
     * @var \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockDefinitionRegistry();

        $this->blockDefinition = new BlockDefinition();
        $this->registry->addBlockDefinition($this->blockDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::addBlockDefinition
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinitions
     */
    public function testAddBlockDefinition()
    {
        self::assertEquals(array('block_definition' => $this->blockDefinition), $this->registry->getBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinition
     */
    public function testGetBlockDefinition()
    {
        self::assertEquals($this->blockDefinition, $this->registry->getBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinition
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockDefinitionThrowsInvalidArgumentException()
    {
        $this->registry->getBlockDefinition('title');
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinition()
    {
        self::assertTrue($this->registry->hasBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinitionWithNoBlockDefinition()
    {
        self::assertFalse($this->registry->hasBlockDefinition('other_block_definition'));
    }
}
