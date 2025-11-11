<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass(ParameterDefinition::class)]
final class ParameterDefinitionTest extends TestCase
{
    public function testSetProperties(): void
    {
        $type = new TextType();
        $constraints = [new NotBlank()];

        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'name',
                'type' => $type,
                'options' => ['option' => 'value'],
                'isRequired' => true,
                'defaultValue' => 42,
                'label' => 'Custom label',
                'groups' => ['group'],
                'constraints' => $constraints,
            ],
        );

        self::assertSame('name', $parameterDefinition->getName());
        self::assertSame($type, $parameterDefinition->getType());
        self::assertSame(['option' => 'value'], $parameterDefinition->getOptions());
        self::assertTrue($parameterDefinition->hasOption('option'));
        self::assertFalse($parameterDefinition->hasOption('other'));
        self::assertSame('value', $parameterDefinition->getOption('option'));
        self::assertTrue($parameterDefinition->isRequired());
        self::assertSame(42, $parameterDefinition->getDefaultValue());
        self::assertSame('Custom label', $parameterDefinition->getLabel());
        self::assertSame(['group'], $parameterDefinition->getGroups());
        self::assertSame($constraints, $parameterDefinition->getConstraints());

        try {
            $parameterDefinition->getOption('other');

            self::fail('Non existing option was returned.');
        } catch (ParameterException) {
            // Do nothing
        }
    }
}
