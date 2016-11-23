<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\RangeType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class RangeTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new RangeType();
        $this->assertEquals('range', $type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::configureOptions
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
        $parameter = $this->getParameter($options, $required, $defaultValue);
        $this->assertEquals($expected, $parameter->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        $this->assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameter($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Parameter('name', new RangeType(), $options, $required, $defaultValue);
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

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $type = new RangeType();
        $parameter = $this->getParameter(array('min' => 5, 'max' => 10), $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('12', false, false),
            array(true, false, false),
            array(array(), false, false),
            array(12, false, false),
            array(12.3, false, false),
            array(0, false, false),
            array(-12, false, false),
            array(5, false, true),
            array(7, false, true),
            array(7.5, false, true),
            array(10, false, true),
            array(null, false, true),
            array(5, true, true),
            array(7, true, true),
            array(7.5, true, true),
            array(10, true, true),
            array(null, true, false),
        );
    }
}
