<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\ChoiceType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ChoiceTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new ChoiceType();
        $this->assertEquals('choice', $type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::configureOptions
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
        return new Parameter(
            array(
                'name' => 'name',
                'type' => new ChoiceType(),
                'options' => $options,
                'isRequired' => $required,
                'defaultValue' => $defaultValue,
            )
        );
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

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $type = new ChoiceType();
        $parameter = $this->getParameter(array('options' => array('One' => 1, 'Two' => 2)));
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidationWithClosure($value, $isValid)
    {
        $closure = function () {
            return array('One' => 1, 'Two' => 2);
        };

        $type = new ChoiceType();
        $parameter = $this->getParameter(array('options' => $closure));
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
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

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     * @param bool $multiple
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, $convertedValue, $multiple)
    {
        $type = new ChoiceType();

        $this->assertEquals(
            $convertedValue,
            $type->fromHash(
                new Parameter(
                    array(
                        'type' => $type,
                        'options' => array(
                            'multiple' => $multiple,
                            'options' => array(42 => 42),
                        ),
                    )
                ),
                $value
            )
        );
    }

    public function fromHashProvider()
    {
        return array(
            array(
                null,
                null,
                false,
            ),
            array(
                array(),
                null,
                false,
            ),
            array(
                42,
                42,
                false,
            ),
            array(
                array(42, 43),
                42,
                false,
            ),
            array(
                null,
                null,
                true,
            ),
            array(
                array(),
                null,
                true,
            ),
            array(
                42,
                array(42),
                true,
            ),
            array(
                array(42, 43),
                array(42, 43),
                true,
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $type = new ChoiceType();
        $this->assertEquals($isEmpty, $type->isValueEmpty(new Parameter(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return array(
            array(null, true),
            array(array(), true),
            array(42, false),
            array(array(42), false),
            array(0, false),
            array('42', false),
            array('', false),
        );
    }
}
