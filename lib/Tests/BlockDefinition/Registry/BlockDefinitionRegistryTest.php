<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Registry;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry;

class BlockDefinitionRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockDefinitionRegistry();
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::addBlockDefinition
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinitions
     */
    public function testAddBlockDefinition()
    {
        $blockDefinition = new BlockDefinition();
        $this->registry->addBlockDefinition($blockDefinition);

        self::assertEquals(array('block_definition' => $blockDefinition), $this->registry->getBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinition
     */
    public function testGetBlockDefinition()
    {
        $blockDefinition = new BlockDefinition();
        $this->registry->addBlockDefinition($blockDefinition);

        self::assertEquals($blockDefinition, $this->registry->getBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinition
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockDefinitionThrowsInvalidArgumentException()
    {
        $blockDefinition = new BlockDefinition();
        $this->registry->addBlockDefinition($blockDefinition);

        $this->registry->getBlockDefinition('title');
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinition()
    {
        $blockDefinition = new BlockDefinition();
        $this->registry->addBlockDefinition($blockDefinition);

        self::assertTrue($this->registry->hasBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinitionWithNoBlockDefinition()
    {
        $blockDefinition = new BlockDefinition();
        $this->registry->addBlockDefinition($blockDefinition);

        self::assertFalse($this->registry->hasBlockDefinition('other_block_definition'));
    }
}
