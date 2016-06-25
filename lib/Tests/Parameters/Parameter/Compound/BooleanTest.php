<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter\Compound;

use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter();
        self::assertEquals('compound_boolean', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::getDefaultValue
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
        self::assertEquals($expected, $parameter->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        self::assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
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
                array(),
                array(),
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
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::getValueConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean::getRequiredConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $parameter = $this->getParameter(array(), $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $parameter->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
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
            array(12.3, false, false),
            array(true, false, true),
            array(false, false, true),
            array(null, false, true),
            array(true, true, true),
            array(false, true, true),
            array(null, true, false),
            array(array(), false, false),
            array(12, false, false),
        );
    }
}
