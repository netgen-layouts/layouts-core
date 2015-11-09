<?php

namespace Netgen\BlockManager\Registry\Tests\BlockDefinitionRegistry;

use Netgen\BlockManager\BlockDefinition\Definition\Paragraph;
use Netgen\BlockManager\Registry\BlockDefinitionRegistry\ArrayBased;
use PHPUnit_Framework_TestCase;

class ArrayBasedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Registry\BlockDefinitionRegistry\ArrayBased::addBlockDefinition
     * @covers \Netgen\BlockManager\Registry\BlockDefinitionRegistry\ArrayBased::getBlockDefinitions
     */
    public function testAddBlockDefinition()
    {
        $registry = new ArrayBased();

        $blockDefinition = new Paragraph();
        $registry->addBlockDefinition($blockDefinition);

        self::assertEquals(array('paragraph' => $blockDefinition), $registry->getBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Registry\BlockDefinitionRegistry\ArrayBased::getBlockDefinition
     */
    public function testGetBlockDefinition()
    {
        $registry = new ArrayBased();

        $blockDefinition = new Paragraph();
        $registry->addBlockDefinition($blockDefinition);

        self::assertEquals($blockDefinition, $registry->getBlockDefinition('paragraph'));
    }

    /**
     * @covers \Netgen\BlockManager\Registry\BlockDefinitionRegistry\ArrayBased::getBlockDefinition
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockDefinitionThrowsInvalidArgumentException()
    {
        $registry = new ArrayBased();

        $blockDefinition = new Paragraph();
        $registry->addBlockDefinition($blockDefinition);

        $registry->getBlockDefinition('title');
    }
}
