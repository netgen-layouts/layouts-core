<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ParameterTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\Parameter::__toString
     * @covers \Netgen\Layouts\Parameters\Parameter::getName
     * @covers \Netgen\Layouts\Parameters\Parameter::getParameterDefinition
     * @covers \Netgen\Layouts\Parameters\Parameter::getValue
     * @covers \Netgen\Layouts\Parameters\Parameter::isEmpty
     */
    public function testSetProperties(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(['isRequired' => false]);

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
