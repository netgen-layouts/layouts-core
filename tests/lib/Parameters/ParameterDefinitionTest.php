<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\Compound\BooleanType;
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

        self::assertSame('name', $parameterDefinition->name);
        self::assertSame($type, $parameterDefinition->type);
        self::assertSame(['option' => 'value'], $parameterDefinition->options);
        self::assertTrue($parameterDefinition->hasOption('option'));
        self::assertFalse($parameterDefinition->hasOption('other'));
        self::assertSame('value', $parameterDefinition->getOption('option'));
        self::assertTrue($parameterDefinition->isRequired);
        self::assertFalse($parameterDefinition->isCompound);
        self::assertSame(42, $parameterDefinition->defaultValue);
        self::assertSame('Custom label', $parameterDefinition->label);
        self::assertSame(['group'], $parameterDefinition->groups);
        self::assertSame($constraints, $parameterDefinition->constraints);

        try {
            $parameterDefinition->getOption('other');

            self::fail('Non existing option was returned.');
        } catch (ParameterException) {
            // Do nothing
        }
    }

    public function testSetPropertiesForCompoundParameterType(): void
    {
        $type = new BooleanType();
        $innerDefinition = new ParameterDefinition();

        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $type,
                'parameterDefinitions' => ['name' => $innerDefinition],
            ],
        );

        self::assertSame($type, $parameterDefinition->type);
        self::assertTrue($parameterDefinition->isCompound);
        self::assertSame(['name' => $innerDefinition], $parameterDefinition->parameterDefinitions);

        self::assertFalse($parameterDefinition->hasParameterDefinition('test'));
        self::assertTrue($parameterDefinition->hasParameterDefinition('name'));

        try {
            $parameterDefinition->getParameterDefinition('test');
            self::fail('Fetched a parameter in empty collection.');
        } catch (ParameterException) {
            // Do nothing
        }

        self::assertSame($innerDefinition, $parameterDefinition->getParameterDefinition('name'));
    }
}
