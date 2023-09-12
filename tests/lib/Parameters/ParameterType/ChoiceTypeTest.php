<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\ChoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class ChoiceTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new ChoiceType();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('choice', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::configureOptions
     *
     * @param array<string, mixed> $options
     * @param mixed $defaultValue
     * @param mixed $expected
     *
     * @dataProvider defaultValueDataProvider
     */
    public function testGetDefaultValue(array $options, bool $required, $defaultValue, $expected): void
    {
        $parameter = $this->getParameterDefinition($options, $required, $defaultValue);
        self::assertSame($expected, $parameter->getDefaultValue());
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::configureOptions
     *
     * @dataProvider validOptionsDataProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @param mixed[] $options
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::configureOptions
     *
     * @dataProvider invalidOptionsDataProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing default parameter values.
     */
    public static function defaultValueDataProvider(): iterable
    {
        $optionsClosure = static fn (): array => ['Option' => 'option'];

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
    public static function validOptionsDataProvider(): iterable
    {
        $closure = static function (): void {};

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
    public static function invalidOptionsDataProvider(): iterable
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
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::getValueConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isRequired, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition(['options' => ['Null' => null, 'One' => 1, 'Two' => 2]], $isRequired);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::getValueConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidationWithClosure($value, bool $isRequired, bool $isValid): void
    {
        $closure = static fn (): array => ['Null' => null, 'One' => 1, 'Two' => 2];

        $parameter = $this->getParameterDefinition(['options' => $closure], $isRequired);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     */
    public static function validationDataProvider(): iterable
    {
        return [
            [1, false, true],
            ['1', false, false],
            ['One', false, false],
            [2, false, true],
            ['2', false, false],
            ['Two', false, false],
            ['123abc.ASD', false, false],
            [0, false, false],
            ['0', false, false],
            [null, false, true],
            ['Null', false, false],
            [null, true, false],
            ['Null', true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::fromHash
     *
     * @dataProvider fromHashDataProvider
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
                    ],
                ),
                $value,
            ),
        );
    }

    public static function fromHashDataProvider(): iterable
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
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ChoiceType::isValueEmpty
     *
     * @dataProvider emptyDataProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    public static function emptyDataProvider(): iterable
    {
        return [
            [null, true],
            [[], true],
            [42, false],
            [[42], false],
            [0, false],
            ['42', false],
            ['', false],
            [false, false],
        ];
    }
}
