<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition\Integer;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Integer::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition();
        $this->assertEquals('integer', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Integer::getDefaultValue
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Integer::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Integer::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Integer::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Integer::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Integer
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Integer($options, $required, $defaultValue);
    }

    /**
     * Provider for testing default parameter values.
     *
     * @return array
     */
    public function defaultValueProvider()
    {
        return array(
            array(array(), true, null, null),
            array(array('min' => 3), true, null, 3),
            array(array(), false, null, null),
            array(array('min' => 3), false, null, null),
            array(array(), true, 4, 4),
            array(array('min' => 3), true, 4, 4),
            array(array(), false, 4, 4),
            array(array('min' => 3), false, 4, 4),
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
                ),
                array(
                    'max' => null,
                    'min' => null,
                ),
            ),
            array(
                array(
                    'max' => 5,
                ),
                array(
                    'max' => 5,
                    'min' => null,
                ),
            ),
            array(
                array(
                    'max' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                ),
            ),
            array(
                array(
                    'min' => 5,
                ),
                array(
                    'min' => 5,
                    'max' => null,
                ),
            ),
            array(
                array(
                    'min' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                ),
            ),
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
            ),
        );
    }
}
