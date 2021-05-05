<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use DateTimeImmutable;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\Value\LinkValue;
use PHPUnit\Framework\TestCase;

final class ParameterTest extends TestCase
{
    /**
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
            ],
        );

        self::assertSame('param_name', $parameter->getName());
        self::assertSame($parameterDefinition, $parameter->getParameterDefinition());
        self::assertSame(42, $parameter->getValue());
        self::assertFalse($parameter->isEmpty());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Parameter::__toString
     */
    public function testToString(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(['isRequired' => false]);

        $parameter = Parameter::fromArray(
            [
                'name' => 'param_name',
                'parameterDefinition' => $parameterDefinition,
                'value' => 42,
                'isEmpty' => false,
            ],
        );

        self::assertSame('42', (string) $parameter);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Parameter::__toString
     */
    public function testToStringWithArray(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(['isRequired' => false]);

        $parameter = Parameter::fromArray(
            [
                'name' => 'param_name',
                'parameterDefinition' => $parameterDefinition,
                'value' => [42],
                'isEmpty' => false,
            ],
        );

        self::assertSame('', (string) $parameter);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Parameter::__toString
     */
    public function testToStringWithNonStringableObject(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(['isRequired' => false]);

        $parameter = Parameter::fromArray(
            [
                'name' => 'param_name',
                'parameterDefinition' => $parameterDefinition,
                'value' => new DateTimeImmutable(),
                'isEmpty' => false,
            ],
        );

        self::assertSame('', (string) $parameter);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Parameter::__toString
     */
    public function testToStringWithStringableObject(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(['isRequired' => false]);

        $linkValue = LinkValue::fromArray(
            [
                'linkType' => LinkValue::LINK_TYPE_EMAIL,
                'link' => 'info@netgen.io',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
        );

        $parameter = Parameter::fromArray(
            [
                'name' => 'param_name',
                'parameterDefinition' => $parameterDefinition,
                'value' => $linkValue,
                'isEmpty' => false,
            ],
        );

        self::assertSame('info@netgen.io?suffix', (string) $parameter);
    }
}
