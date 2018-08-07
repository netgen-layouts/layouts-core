<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getGroups
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getLabel
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::isRequired
     */
    public function testDefaultProperties(): void
    {
        $parameterDefinition = new ParameterDefinition();

        self::assertSame([], $parameterDefinition->getOptions());
        self::assertFalse($parameterDefinition->isRequired());
        self::assertNull($parameterDefinition->getDefaultValue());
        self::assertNull($parameterDefinition->getLabel());
        self::assertSame([], $parameterDefinition->getGroups());
        self::assertSame([], $parameterDefinition->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getGroups
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getLabel
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getName
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getOption
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getType
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::hasOption
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::isRequired
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
            ]
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
