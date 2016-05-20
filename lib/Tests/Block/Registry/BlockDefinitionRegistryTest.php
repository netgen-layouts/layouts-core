<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;

class BlockDefinitionRegistryTest extends \PHPUnit_Framework_TestCase
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

        $configMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockDefinition = new BlockDefinition(
            'block_definition',
            $this->getMock(BlockDefinitionHandlerInterface::class),
            $configMock
        );

        $this->registry->addBlockDefinition($this->blockDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::addBlockDefinition
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getBlockDefinitions
     */
    public function testAddBlockDefinition()
    {
        self::assertEquals(array('block_definition' => $this->blockDefinition), $this->registry->getBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getBlockDefinition
     */
    public function testGetBlockDefinition()
    {
        self::assertEquals($this->blockDefinition, $this->registry->getBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::getBlockDefinition
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockDefinitionThrowsInvalidArgumentException()
    {
        $this->registry->getBlockDefinition('title');
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinition()
    {
        self::assertTrue($this->registry->hasBlockDefinition('block_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry::hasBlockDefinition
     */
    public function testHasBlockDefinitionWithNoBlockDefinition()
    {
        self::assertFalse($this->registry->hasBlockDefinition('other_block_definition'));
    }
}
