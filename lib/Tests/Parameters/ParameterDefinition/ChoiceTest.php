<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition\Choice;
use PHPUnit\Framework\TestCase;

class ChoiceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Choice::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition(array('options' => array('One' => 1)));
        $this->assertEquals('choice', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Choice::getDefaultValue
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Choice::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Choice::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Choice::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Choice::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Choice
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Choice($options, $required, $defaultValue);
    }

    /**
     * Provider for testing default parameter values.
     *
     * @return array
     */
    public function defaultValueProvider()
    {
        $optionsClosure = function () {
            return array('Option' => 'option');
        };

        return array(
            array(array('options' => array('Option' => 'option')), true, null, 'option'),
            array(array('options' => array('Option' => 'option')), false, null, null),
            array(array('options' => array('Option' => 'option')), true, 'value', 'value'),
            array(array('options' => array('Option' => 'option')), false, 'value', 'value'),
            array(array('options' => $optionsClosure), true, null, null),
            array(array('options' => $optionsClosure), false, null, null),
            array(array('options' => $optionsClosure), true, 'value', 'value'),
            array(array('options' => $optionsClosure), false, 'value', 'value'),
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
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => true,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                    'options' => function () {
                    },
                ),
                array(
                    'multiple' => true,
                    'options' => function () {
                    },
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
                    'multiple' => 'true',
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'options' => 'options',
                ),
            ),
            array(
                array(
                    'options' => array(),
                ),
            ),
            array(
                array(
                    'undefined_value' => 'Value',
                ),
            ),
            array(
                array(),
            ),
        );
    }
}
