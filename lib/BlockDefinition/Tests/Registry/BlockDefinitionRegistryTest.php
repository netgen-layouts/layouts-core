<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Registry;

use Netgen\BlockManager\BlockDefinition\Tests\Stubs\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistry;
use PHPUnit_Framework_TestCase;

class BlockDefinitionRegistryTest extends PHPUnit_Framework_TestCase
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
}
