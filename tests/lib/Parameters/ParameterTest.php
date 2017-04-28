<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
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
     * @covers \Netgen\BlockManager\Parameters\Parameter::getLabel
     * @covers \Netgen\BlockManager\Parameters\Parameter::getGroups
     */
    public function testSetDefaultProperties()
    {
        $parameter = new Parameter();

        $this->assertNull($parameter->getName());
        $this->assertnull($parameter->getType());
        $this->assertNull($parameter->getOptions());
        $this->assertNull($parameter->isRequired());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertNull($parameter->getLabel());
        $this->assertNull($parameter->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     * @covers \Netgen\BlockManager\Parameters\Parameter::getType
     * @covers \Netgen\BlockManager\Parameters\Parameter::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter::hasOption
     * @covers \Netgen\BlockManager\Parameters\Parameter::getOption
     * @covers \Netgen\BlockManager\Parameters\Parameter::isRequired
     * @covers \Netgen\BlockManager\Parameters\Parameter::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\Parameter::getLabel
     * @covers \Netgen\BlockManager\Parameters\Parameter::getGroups
     */
    public function testSetProperties()
    {
        $parameter = new Parameter(
            array(
                'name' => 'name',
                'type' => new TextType(),
                'options' => array('option' => 'value'),
                'isRequired' => true,
                'defaultValue' => 42,
                'label' => 'Custom label',
                'groups' => array('group'),
            )
        );

        $this->assertEquals('name', $parameter->getName());
        $this->assertEquals(new TextType(), $parameter->getType());
        $this->assertEquals(array('option' => 'value'), $parameter->getOptions());
        $this->assertTrue($parameter->hasOption('option'));
        $this->assertFalse($parameter->hasOption('other'));
        $this->assertEquals('value', $parameter->getOption('option'));
        $this->assertTrue($parameter->isRequired());
        $this->assertEquals(42, $parameter->getDefaultValue());
        $this->assertEquals('Custom label', $parameter->getLabel());
        $this->assertEquals(array('group'), $parameter->getGroups());

        try {
            $parameter->getOption('other');

            $this->fail('Non existing option was returned.');
        } catch (ParameterException $e) {
            // Do nothing
        }
    }
}
