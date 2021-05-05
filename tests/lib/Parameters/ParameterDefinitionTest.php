<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::getDefaultValue
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::getGroups
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::getLabel
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::getName
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::getOption
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::getOptions
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::getType
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::hasOption
     * @covers \Netgen\Layouts\Parameters\ParameterDefinition::isRequired
     */
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
        } catch (ParameterException $e) {
            // Do nothing
        }
    }
}
