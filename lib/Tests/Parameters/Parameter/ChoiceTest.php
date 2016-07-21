<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter\Choice;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class ChoiceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter(array('options' => array('One' => 1)));
        $this->assertEquals('choice', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::getDefaultValue
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
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\Parameter\Choice
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
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
        return array(
            array(array('options' => array('Option' => 'option')), true, null, 'option'),
            array(array('options' => array('Option' => 'option')), false, null, null),
            array(array('options' => array('Option' => 'option')), true, 'value', 'value'),
            array(array('options' => array('Option' => 'option')), false, 'value', 'value'),
            array(array('options' => function () {return array('Option' => 'option');}), true, null, null),
            array(array('options' => function () {return array('Option' => 'option');}), false, null, null),
            array(array('options' => function () {return array('Option' => 'option');}), true, 'value', 'value'),
            array(array('options' => function () {return array('Option' => 'option');}), false, 'value', 'value'),
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
                    'options' => function () {},
                ),
                array(
                    'multiple' => true,
                    'options' => function () {},
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

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $parameter = $this->getParameter(array('options' => array('One' => 1, 'Two' => 2)));
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $parameter->getConstraints());
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\Parameter\Choice::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidationWithClosure($value, $isValid)
    {
        $parameter = $this->getParameter(array('options' => function () {return array('One' => 1, 'Two' => 2);}));
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $parameter->getConstraints());
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
            array(1, true),
            array('One', false),
            array(2, true),
            array('Two', false),
            array('123abc.ASD', false),
            array(0, false),
        );
    }
}
