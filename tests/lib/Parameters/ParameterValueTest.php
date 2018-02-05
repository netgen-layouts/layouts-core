<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Parameters\ParameterValue;
use PHPUnit\Framework\TestCase;

final class ParameterValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getName
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getValue
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::isEmpty
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::__toString
     */
    public function testSetDefaultProperties()
    {
        $parameterValue = new ParameterValue();

        $this->assertNull($parameterValue->getName());
        $this->assertNull($parameterValue->getParameterDefinition());
        $this->assertNull($parameterValue->getValue());
        $this->assertNull($parameterValue->isEmpty());
        $this->assertEquals('', (string) $parameterValue);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getName
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getValue
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::isEmpty
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::__toString
     */
    public function testSetProperties()
    {
        $parameterDefinition = new ParameterDefinition(
            array(
                'type' => new TextType(),
            )
        );

        $parameterValue = new ParameterValue(
            array(
                'name' => 'param_name',
                'parameterDefinition' => $parameterDefinition,
                'value' => 42,
                'isEmpty' => false,
            )
        );

        $this->assertEquals('param_name', $parameterValue->getName());
        $this->assertEquals($parameterDefinition, $parameterValue->getParameterDefinition());
        $this->assertEquals(42, $parameterValue->getValue());
        $this->assertFalse($parameterValue->isEmpty());
        $this->assertEquals('42', (string) $parameterValue);
    }
}
