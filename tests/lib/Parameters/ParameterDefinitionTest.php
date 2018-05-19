<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getGroups
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getLabel
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::isRequired
     */
    public function testSetDefaultProperties()
    {
        $parameterDefinition = new ParameterDefinition();

        $this->assertEquals([], $parameterDefinition->getOptions());
        $this->assertFalse($parameterDefinition->isRequired());
        $this->assertNull($parameterDefinition->getDefaultValue());
        $this->assertNull($parameterDefinition->getLabel());
        $this->assertEquals([], $parameterDefinition->getGroups());
        $this->assertEquals([], $parameterDefinition->getConstraints());
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
    public function testSetProperties()
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'name' => 'name',
                'type' => new TextType(),
                'options' => ['option' => 'value'],
                'isRequired' => true,
                'defaultValue' => 42,
                'label' => 'Custom label',
                'groups' => ['group'],
                'constraints' => [new NotBlank()],
            ]
        );

        $this->assertEquals('name', $parameterDefinition->getName());
        $this->assertEquals(new TextType(), $parameterDefinition->getType());
        $this->assertEquals(['option' => 'value'], $parameterDefinition->getOptions());
        $this->assertTrue($parameterDefinition->hasOption('option'));
        $this->assertFalse($parameterDefinition->hasOption('other'));
        $this->assertEquals('value', $parameterDefinition->getOption('option'));
        $this->assertTrue($parameterDefinition->isRequired());
        $this->assertEquals(42, $parameterDefinition->getDefaultValue());
        $this->assertEquals('Custom label', $parameterDefinition->getLabel());
        $this->assertEquals(['group'], $parameterDefinition->getGroups());
        $this->assertEquals([new NotBlank()], $parameterDefinition->getConstraints());

        try {
            $parameterDefinition->getOption('other');

            $this->fail('Non existing option was returned.');
        } catch (ParameterException $e) {
            // Do nothing
        }
    }
}
