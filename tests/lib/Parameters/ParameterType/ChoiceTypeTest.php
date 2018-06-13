<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\ChoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ChoiceTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp()
    {
        $this->type = new ChoiceType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('choice', $this->type->getIdentifier());
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
        $parameter = $this->getParameterDefinition($options, $required, $defaultValue);
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
        $parameter = $this->getParameterDefinition($options);
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
        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing default parameter values.
     *
     * @return array
     */
    public function defaultValueProvider()
    {
        $optionsClosure = function () {
            return ['Option' => 'option'];
        };

        return [
            [['options' => ['Option' => 'option']], true, null, 'option'],
            [['options' => ['Option' => 'option']], false, null, null],
            [['options' => ['Option' => 'option']], true, 'value', 'value'],
            [['options' => ['Option' => 'option']], false, 'value', 'value'],
            [['options' => $optionsClosure], true, null, null],
            [['options' => $optionsClosure], false, null, null],
            [['options' => $optionsClosure], true, 'value', 'value'],
            [['options' => $optionsClosure], false, 'value', 'value'],
        ];
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return [
            [
                [
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
            ],
            [
                [
                    'multiple' => false,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
            ],
            [
                [
                    'multiple' => true,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
                [
                    'multiple' => true,
                    'expanded' => false,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
            ],
            [
                [
                    'expanded' => false,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
            ],
            [
                [
                    'expanded' => true,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
                [
                    'multiple' => false,
                    'expanded' => true,
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
            ],
            [
                [
                    'options' => function () {
                    },
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'options' => function () {
                    },
                ],
            ],
        ];
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return [
            [
                [
                    'multiple' => 'true',
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
            ],
            [
                [
                    'expanded' => 'true',
                    'options' => [
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ],
                ],
            ],
            [
                [
                    'options' => 'options',
                ],
            ],
            [
                [
                    'options' => [],
                ],
            ],
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
            [
                [],
            ],
        ];
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
        $parameter = $this->getParameterDefinition(['options' => ['One' => 1, 'Two' => 2]]);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
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
            return ['One' => 1, 'Two' => 2];
        };

        $parameter = $this->getParameterDefinition(['options' => $closure]);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            [1, true],
            ['One', false],
            [2, true],
            ['Two', false],
            ['123abc.ASD', false],
            [0, false],
        ];
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
        $this->assertEquals(
            $convertedValue,
            $this->type->fromHash(
                $this->getParameterDefinition(
                    [
                        'multiple' => $multiple,
                        'options' => [42 => 42],
                    ]
                ),
                $value
            )
        );
    }

    public function fromHashProvider()
    {
        return [
            [
                null,
                null,
                false,
            ],
            [
                [],
                null,
                false,
            ],
            [
                42,
                42,
                false,
            ],
            [
                [42, 43],
                42,
                false,
            ],
            [
                null,
                null,
                true,
            ],
            [
                [],
                null,
                true,
            ],
            [
                42,
                [42],
                true,
            ],
            [
                [42, 43],
                [42, 43],
                true,
            ],
        ];
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
        $this->assertEquals($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return [
            [null, true],
            [[], true],
            [42, false],
            [[42], false],
            [0, false],
            ['42', false],
            ['', false],
        ];
    }
}
