<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;

class ParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::isRequired
     */
    public function testGetIsRequired()
    {
        $parameterDefinition = new ParameterDefinition(array(), true);

        $this->assertTrue($parameterDefinition->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::isRequired
     */
    public function testGetIsRequiredReturnsFalse()
    {
        $parameterDefinition = new ParameterDefinition();

        $this->assertFalse($parameterDefinition->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getDefaultValue
     */
    public function testGetDefaultValue()
    {
        $parameterDefinition = new ParameterDefinition();

        $this->assertEquals(null, $parameterDefinition->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getGroups
     */
    public function testGetDefaultGroups()
    {
        $parameterDefinition = new ParameterDefinition();

        $this->assertEquals(array(), $parameterDefinition->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getGroups
     */
    public function testGetGroups()
    {
        $parameterDefinition = new ParameterDefinition(array(), false, null, array('group'));

        $this->assertEquals(array('group'), $parameterDefinition->getGroups());
    }
}
