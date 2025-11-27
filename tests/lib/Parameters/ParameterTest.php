<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use DateTimeImmutable;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\Value\LinkType;
use Netgen\Layouts\Parameters\Value\LinkValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameter::class)]
final class ParameterTest extends TestCase
{
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

        self::assertSame('param_name', $parameter->name);
        self::assertSame($parameterDefinition, $parameter->parameterDefinition);
        self::assertSame(42, $parameter->value);
        self::assertFalse($parameter->isEmpty);
    }

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

    public function testToStringWithStringableObject(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(['isRequired' => false]);

        $linkValue = LinkValue::fromArray(
            [
                'linkType' => LinkType::Email,
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
