<?php

namespace Netgen\BlockManager\Tests\Layout\Container\Registry;

use Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry;
use Netgen\BlockManager\Tests\Layout\Container\Stubs\ContainerDefinition;
use PHPUnit\Framework\TestCase;

class ContainerDefinitionRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface
     */
    protected $containerDefinition;

    /**
     * @var \Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ContainerDefinitionRegistry();

        $this->containerDefinition = new ContainerDefinition('container_definition');

        $this->registry->addContainerDefinition('container_definition', $this->containerDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry::addContainerDefinition
     * @covers \Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry::getContainerDefinitions
     */
    public function testAddContainerDefinition()
    {
        $this->assertEquals(array('container_definition' => $this->containerDefinition), $this->registry->getContainerDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry::getContainerDefinition
     */
    public function testGetContainerDefinition()
    {
        $this->assertEquals($this->containerDefinition, $this->registry->getContainerDefinition('container_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry::getContainerDefinition
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetContainerDefinitionThrowsInvalidArgumentException()
    {
        $this->registry->getContainerDefinition('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry::hasContainerDefinition
     */
    public function testHasContainerDefinition()
    {
        $this->assertTrue($this->registry->hasContainerDefinition('container_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\Registry\ContainerDefinitionRegistry::hasContainerDefinition
     */
    public function testHasContainerDefinitionWithNoContainerDefinition()
    {
        $this->assertFalse($this->registry->hasContainerDefinition('other_container_definition'));
    }
}
