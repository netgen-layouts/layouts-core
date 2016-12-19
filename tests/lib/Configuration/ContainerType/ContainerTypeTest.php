<?php

namespace Netgen\BlockManager\Tests\Configuration\ContainerType;

use Netgen\BlockManager\Configuration\ContainerType\ContainerType;
use Netgen\BlockManager\Tests\Layout\Container\Stubs\ContainerDefinition;
use PHPUnit\Framework\TestCase;

class ContainerTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\ContainerType\ContainerType
     */
    protected $containerType;

    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface
     */
    protected $containerDefinition;

    public function setUp()
    {
        $this->containerDefinition = new ContainerDefinition('container');

        $this->containerType = new ContainerType(
            array(
                'identifier' => 'container',
                'name' => 'Container',
                'containerDefinition' => $this->containerDefinition,
                'defaults' => array(
                    'name' => 'Name',
                    'view_type' => 'default',
                    'parameters' => array('tag' => 'h3'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::__construct
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('container', $this->containerType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Container', $this->containerType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getContainerDefinition
     */
    public function testGetContainerDefinition()
    {
        $this->assertEquals($this->containerDefinition, $this->containerType->getContainerDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getDefaults
     */
    public function testGetDefaults()
    {
        $this->assertEquals(
            array(
                'name' => 'Name',
                'view_type' => 'default',
                'parameters' => array('tag' => 'h3'),
            ),
            $this->containerType->getDefaults()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getDefaultName
     */
    public function testGetDefaultName()
    {
        $this->assertEquals('Name', $this->containerType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getDefaultViewType
     */
    public function testGetDefaultViewType()
    {
        $this->assertEquals('default', $this->containerType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getDefaultParameters
     */
    public function testGetDefaultParameters()
    {
        $this->assertEquals(array('tag' => 'h3'), $this->containerType->getDefaultParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getDefaultName
     */
    public function testGetDefaultEmptyName()
    {
        $this->containerType = new ContainerType();

        $this->assertEquals('', $this->containerType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getDefaultViewType
     */
    public function testGetDefaultEmptyViewType()
    {
        $this->containerType = new ContainerType();

        $this->assertEquals('', $this->containerType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerType\ContainerType::getDefaultParameters
     */
    public function testGetDefaultEmptyParameters()
    {
        $this->containerType = new ContainerType();

        $this->assertEquals(array(), $this->containerType->getDefaultParameters());
    }
}
