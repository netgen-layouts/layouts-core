<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ParameterTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__toString
     * @covers \Netgen\BlockManager\Parameters\Parameter::getValue
     * @covers \Netgen\BlockManager\Parameters\Parameter::isEmpty
     */
    public function testSetDefaultProperties(): void
    {
        $parameter = new Parameter();

        $this->assertNull($parameter->getValue());
        $this->assertTrue($parameter->isEmpty());
        $this->assertSame('', (string) $parameter);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__toString
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     * @covers \Netgen\BlockManager\Parameters\Parameter::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\Parameter::getValue
     * @covers \Netgen\BlockManager\Parameters\Parameter::isEmpty
     */
    public function testSetProperties(): void
    {
        $parameterDefinition = new ParameterDefinition();

        $parameter = Parameter::fromArray(
            [
                'name' => 'param_name',
                'parameterDefinition' => $parameterDefinition,
                'value' => 42,
                'isEmpty' => false,
            ]
        );

        $this->assertSame('param_name', $parameter->getName());
        $this->assertSame($parameterDefinition, $parameter->getParameterDefinition());
        $this->assertSame(42, $parameter->getValue());
        $this->assertFalse($parameter->isEmpty());
        $this->assertSame('42', (string) $parameter);
    }
}
