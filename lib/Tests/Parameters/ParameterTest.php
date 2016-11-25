<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     * @covers \Netgen\BlockManager\Parameters\Parameter::getType
     * @covers \Netgen\BlockManager\Parameters\Parameter::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter::isRequired
     * @covers \Netgen\BlockManager\Parameters\Parameter::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\Parameter::getGroups
     */
    public function testSetDefaultProperties()
    {
        $parameter = new Parameter('name', new TextType());

        $this->assertEquals('name', $parameter->getName());
        $this->assertEquals(new TextType(), $parameter->getType());
        $this->assertEquals(array(), $parameter->getOptions());
        $this->assertFalse($parameter->isRequired());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertEquals(array(), $parameter->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     * @covers \Netgen\BlockManager\Parameters\Parameter::getType
     * @covers \Netgen\BlockManager\Parameters\Parameter::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter::isRequired
     * @covers \Netgen\BlockManager\Parameters\Parameter::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\Parameter::getGroups
     */
    public function testSetProperties()
    {
        $parameter = new Parameter(
            'name',
            new TextType(),
            array(
                'required' => true,
                'default_value' => 42,
                'groups' => array('group'),
            )
        );

        $this->assertEquals('name', $parameter->getName());
        $this->assertEquals(new TextType(), $parameter->getType());
        $this->assertTrue($parameter->isRequired());
        $this->assertEquals(42, $parameter->getDefaultValue());
        $this->assertEquals(array('group'), $parameter->getGroups());

        $this->assertEquals(
            array(
                'required' => true,
                'default_value' => 42,
                'groups' => array('group'),
            ),
            $parameter->getOptions()
        );
    }
}
