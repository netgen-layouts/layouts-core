<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ParameterTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     * @covers \Netgen\BlockManager\Parameters\Parameter::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\Parameter::getValue
     * @covers \Netgen\BlockManager\Parameters\Parameter::isEmpty
     * @covers \Netgen\BlockManager\Parameters\Parameter::__toString
     */
    public function testSetDefaultProperties()
    {
        $parameter = new Parameter();

        $this->assertNull($parameter->getName());
        $this->assertNull($parameter->getParameterDefinition());
        $this->assertNull($parameter->getValue());
        $this->assertNull($parameter->isEmpty());
        $this->assertEquals('', (string) $parameter);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     * @covers \Netgen\BlockManager\Parameters\Parameter::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\Parameter::getValue
     * @covers \Netgen\BlockManager\Parameters\Parameter::isEmpty
     * @covers \Netgen\BlockManager\Parameters\Parameter::__toString
     */
    public function testSetProperties()
    {
        $parameter = new Parameter(
            array(
                'name' => 'param_name',
                'parameterDefinition' => new ParameterDefinition(),
                'value' => 42,
                'isEmpty' => false,
            )
        );

        $this->assertEquals('param_name', $parameter->getName());
        $this->assertEquals(new ParameterDefinition(), $parameter->getParameterDefinition());
        $this->assertEquals(42, $parameter->getValue());
        $this->assertFalse($parameter->isEmpty());
        $this->assertEquals('42', (string) $parameter);
    }
}
