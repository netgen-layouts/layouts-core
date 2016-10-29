<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition\Range;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Range::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition(array('min' => 5, 'max' => 10));
        $this->assertEquals('range', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Range::getDefaultValue
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Range::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Range::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Range::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Range::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Range
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Range($options, $required, $defaultValue);
    }

    /**
     * Provider for testing default parameter values.
     *
     * @return array
     */
    public function defaultValueProvider()
    {
        return array(
            array(array('min' => 3, 'max' => 5), true, null, 3),
            array(array('min' => 3, 'max' => 5), false, null, null),
            array(array('min' => 3, 'max' => 5), true, 4, 4),
            array(array('min' => 3, 'max' => 5), false, 4, 4),
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
                    'min' => 5,
                    'max' => 10,
                ),
                array(
                    'min' => 5,
                    'max' => 10,
                ),
            ),
            array(
                array(
                    'min' => 5,
                    'max' => 3,
                ),
                array(
                    'min' => 5,
                    'max' => 5,
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
                    'max' => array(),
                ),
                array(
                    'max' => 5.5,
                ),
                array(
                    'max' => '5',
                ),
                array(
                    'min' => array(),
                ),
                array(
                    'min' => 5.5,
                ),
                array(
                    'min' => '5',
                ),
                array(
                    'undefined_value' => 'Value',
                ),
                array(
                ),
                array(
                    'max' => 5,
                ),
                array(
                    'max' => null,
                ),
                array(
                    'min' => 5,
                ),
                array(
                    'min' => null,
                ),
                array(
                    'min' => null,
                    'max' => 5,
                ),
                array(
                    'min' => 5,
                    'max' => null,
                ),
            ),
        );
    }
}
