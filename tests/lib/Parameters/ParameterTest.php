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

        self::assertNull($parameter->getValue());
        self::assertTrue($parameter->isEmpty());
        self::assertSame('', (string) $parameter);
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

        self::assertSame('param_name', $parameter->getName());
        self::assertSame($parameterDefinition, $parameter->getParameterDefinition());
        self::assertSame(42, $parameter->getValue());
        self::assertFalse($parameter->isEmpty());
        self::assertSame('42', (string) $parameter);
    }
}
