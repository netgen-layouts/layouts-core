<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Registry;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry;

class BlockDefinitionRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::addBlockDefinition
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinitions
     */
    public function testAddBlockDefinition()
    {
        $registry = new BlockDefinitionRegistry();

        $blockDefinition = new BlockDefinition();
        $registry->addBlockDefinition($blockDefinition);

        self::assertEquals(array('block_definition' => $blockDefinition), $registry->getBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinition
     */
    public function testGetBlockDefinition()
    {
        $registry = new BlockDefinitionRegistry();

        $blockDefinition = new BlockDefinition();
        $registry->addBlockDefinition($blockDefinition);

        self::assertEquals($blockDefinition, $registry->getBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::getBlockDefinition
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockDefinitionThrowsInvalidArgumentException()
    {
        $registry = new BlockDefinitionRegistry();

        $blockDefinition = new BlockDefinition();
        $registry->addBlockDefinition($blockDefinition);

        $registry->getBlockDefinition('title');
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinition()
    {
        $registry = new BlockDefinitionRegistry();

        $blockDefinition = new BlockDefinition();
        $registry->addBlockDefinition($blockDefinition);

        self::assertEquals(true, $registry->hasBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinitionWithNoBlockDefinition()
    {
        $registry = new BlockDefinitionRegistry();

        $blockDefinition = new BlockDefinition();
        $registry->addBlockDefinition($blockDefinition);

        self::assertEquals(false, $registry->hasBlockDefinition('other_block_definition'));
    }
}
