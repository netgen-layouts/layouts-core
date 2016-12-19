<?php

namespace Netgen\BlockManager\Tests\Configuration\Registry;

use Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry;
use Netgen\BlockManager\Tests\Configuration\Stubs\ContainerType;
use PHPUnit\Framework\TestCase;

class ContainerTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\ContainerType\ContainerType
     */
    protected $containerType;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ContainerTypeRegistry();

        $this->containerType = new ContainerType(array('identifier' => 'container_type'));

        $this->registry->addContainerType($this->containerType);
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry::addContainerType
     * @covers \Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry::getContainerTypes
     */
    public function testAddContainerType()
    {
        $this->assertEquals(array('container_type' => $this->containerType), $this->registry->getContainerTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry::hasContainerType
     */
    public function testHasContainerType()
    {
        $this->assertTrue($this->registry->hasContainerType('container_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry::hasContainerType
     */
    public function testHasContainerTypeWithNoContainerType()
    {
        $this->assertFalse($this->registry->hasContainerType('other_container_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry::getContainerType
     */
    public function testGetContainerType()
    {
        $this->assertEquals($this->containerType, $this->registry->getContainerType('container_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\ContainerTypeRegistry::getContainerType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetContainerTypeThrowsInvalidArgumentException()
    {
        $this->registry->getContainerType('other_container_type');
    }
}
