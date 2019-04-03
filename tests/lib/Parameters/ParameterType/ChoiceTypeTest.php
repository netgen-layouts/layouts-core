<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\ChoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class ChoiceTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp(): void
    {
        $this->type = new ChoiceType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('choice', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::configureOptions
     *
     * @param array<string, mixed> $options
     * @param bool $required
     * @param mixed $defaultValue
     * @param mixed $expected
     *
     * @dataProvider defaultValueProvider
     */
    public function testGetDefaultValue(array $options, bool $required, $defaultValue, $expected): void
    {
        $parameter = $this->getParameterDefinition($options, $required, $defaultValue);
        self::assertSame($expected, $parameter->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::configureOptions
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing default parameter values.
     */
    public function defaultValueProvider(): array
    {
        $optionsClosure = function (): array {
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
     */
    public function validOptionsProvider(): array
    {
        $closure = function (): void {};

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
                    'options' => $closure,
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'options' => $closure,
                ],
            ],
        ];
    }

    /**
     * Provider for testing invalid parameter attributes.
     */
    public function invalidOptionsProvider(): array
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
    public function testValidation($value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition(['options' => ['One' => 1, 'Two' => 2]]);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ChoiceType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidationWithClosure($value, bool $isValid): void
    {
        $closure = function (): array {
            return ['One' => 1, 'Two' => 2];
        };

        $parameter = $this->getParameterDefinition(['options' => $closure]);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     */
    public function validationProvider(): array
    {
        return [
            [1, true],
            ['1', false],
            ['One', false],
            [2, true],
            ['2', false],
            ['Two', false],
            ['123abc.ASD', false],
            [0, false],
            ['0', false],
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
    public function testFromHash($value, $convertedValue, bool $multiple): void
    {
        self::assertSame(
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

    public function fromHashProvider(): array
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
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    public function emptyProvider(): array
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
