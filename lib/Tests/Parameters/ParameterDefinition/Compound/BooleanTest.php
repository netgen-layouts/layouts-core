<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterDefinition\Compound;

use Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition();
        $this->assertEquals('compound_boolean', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean::getDefaultValue
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     * @param mixed $expected
     *
     * @dataProvider defaultValueProvider
     */
    public function testGetDefaultValue(array $options, $required, $defaultValue, $expected)
    {
        $parameterDefinition = $this->getParameterDefinition($options, $required, $defaultValue);
        $this->assertEquals($expected, $parameterDefinition->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameterDefinition = $this->getParameterDefinition($options);
        $this->assertEquals($resolvedOptions, $parameterDefinition->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameterDefinition($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Compound\Boolean
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Boolean(array(), $options, $required, $defaultValue);
    }

    /**
     * Provider for testing default parameter values.
     *
     * @return array
     */
    public function defaultValueProvider()
    {
        return array(
            array(array(), true, null, false),
            array(array(), false, null, null),
            array(array(), true, false, false),
            array(array(), false, false, false),
            array(array(), true, true, true),
            array(array(), false, true, true),
        );
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return array(
            array(
                array(
                    'reverse' => false,
                ),
                array(
                    'reverse' => false,
                ),
            ),
            array(
                array(
                    'reverse' => true,
                ),
                array(
                    'reverse' => true,
                ),
            ),
            array(
                array(),
                array(
                    'reverse' => false,
                ),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'reverse' => 'true',
                ),
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }
}
