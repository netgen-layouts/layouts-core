<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
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

        $configMock = $this->createMock(Configuration::class);

        $this->blockDefinition = new BlockDefinition(
            'block_definition',
            new BlockDefinitionHandler(),
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
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
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
