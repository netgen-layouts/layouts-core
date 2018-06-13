<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\NumberType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class NumberTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp()
    {
        $this->type = new NumberType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\NumberType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('number', $this->type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\NumberType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\NumberType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\NumberType::configureOptions
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
        return [
            [[], true, null, null],
            [['min' => 3], true, null, 3],
            [[], false, null, null],
            [['min' => 3], false, null, null],
            [[], true, 4, 4],
            [['min' => 3], true, 4, 4],
            [[], false, 4, 4],
            [['min' => 3], false, 4, 4],
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
                ],
                [
                    'max' => null,
                    'min' => null,
                    'scale' => 3,
                ],
            ],
            [
                [
                    'max' => 5,
                ],
                [
                    'max' => 5,
                    'min' => null,
                    'scale' => 3,
                ],
            ],
            [
                [
                    'max' => null,
                ],
                [
                    'max' => null,
                    'min' => null,
                    'scale' => 3,
                ],
            ],
            [
                [
                    'min' => 5,
                ],
                [
                    'min' => 5,
                    'max' => null,
                    'scale' => 3,
                ],
            ],
            [
                [
                    'min' => null,
                ],
                [
                    'max' => null,
                    'min' => null,
                    'scale' => 3,
                ],
            ],
            [
                [
                    'min' => 5,
                    'max' => 10,
                ],
                [
                    'min' => 5,
                    'max' => 10,
                    'scale' => 3,
                ],
            ],
            [
                [
                    'min' => 5,
                    'max' => 3,
                ],
                [
                    'min' => 5,
                    'max' => 5,
                    'scale' => 3,
                ],
            ],
            [
                [
                    'scale' => 5,
                ],
                [
                    'min' => null,
                    'max' => null,
                    'scale' => 5,
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
                    'max' => [],
                ],
                [
                    'max' => 5.5,
                ],
                [
                    'max' => '5',
                ],
                [
                    'min' => [],
                ],
                [
                    'min' => 5.5,
                ],
                [
                    'min' => '5',
                ],
                [
                    'min' => [],
                ],
                [
                    'min' => 5.5,
                ],
                [
                    'min' => '5',
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\NumberType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $parameter = $this->getParameterDefinition(['min' => 5, 'max' => 10], $required);
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
            ['12', false, false],
            [true, false, false],
            [[], false, false],
            [12, false, false],
            [12.3, false, false],
            [0, false, false],
            [-12, false, false],
            [5, false, true],
            [7, false, true],
            [7.5, false, true],
            [10, false, true],
            [null, false, true],
            [5, true, true],
            [7, true, true],
            [7.5, true, true],
            [10, true, true],
            [null, true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\NumberType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $this->assertEquals($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
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
            [42, false],
            [42.5, false],
            [0, false],
        ];
    }
}
