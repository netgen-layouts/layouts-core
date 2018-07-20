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

        $this->assertSame([], $parameterDefinition->getOptions());
        $this->assertFalse($parameterDefinition->isRequired());
        $this->assertNull($parameterDefinition->getDefaultValue());
        $this->assertNull($parameterDefinition->getLabel());
        $this->assertSame([], $parameterDefinition->getGroups());
        $this->assertSame([], $parameterDefinition->getConstraints());
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

        $this->assertSame('name', $parameterDefinition->getName());
        $this->assertSame($type, $parameterDefinition->getType());
        $this->assertSame(['option' => 'value'], $parameterDefinition->getOptions());
        $this->assertTrue($parameterDefinition->hasOption('option'));
        $this->assertFalse($parameterDefinition->hasOption('other'));
        $this->assertSame('value', $parameterDefinition->getOption('option'));
        $this->assertTrue($parameterDefinition->isRequired());
        $this->assertSame(42, $parameterDefinition->getDefaultValue());
        $this->assertSame('Custom label', $parameterDefinition->getLabel());
        $this->assertSame(['group'], $parameterDefinition->getGroups());
        $this->assertSame($constraints, $parameterDefinition->getConstraints());

        try {
            $parameterDefinition->getOption('other');

            $this->fail('Non existing option was returned.');
        } catch (ParameterException $e) {
            // Do nothing
        }
    }
}
