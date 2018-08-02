<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\RangeType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class RangeTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp(): void
    {
        $this->type = new RangeType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('range', $this->type::getIdentifier());
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
    public function testGetDefaultValue(array $options, bool $required, $defaultValue, $expected): void
    {
        $parameter = $this->getParameterDefinition($options, $required, $defaultValue);
        $this->assertSame($expected, $parameter->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->getParameterDefinition($options);
    }

    public function defaultValueProvider(): array
    {
        return [
            [['min' => 3, 'max' => 5], true, null, 3],
            [['min' => 3, 'max' => 5], false, null, null],
            [['min' => 3, 'max' => 5], true, 4, 4],
            [['min' => 3, 'max' => 5], false, 4, 4],
        ];
    }

    public function validOptionsProvider(): array
    {
        return [
            [
                [
                    'min' => 5,
                    'max' => 10,
                ],
                [
                    'min' => 5,
                    'max' => 10,
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
                ],
            ],
        ];
    }

    public function invalidOptionsProvider(): array
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
                    'undefined_value' => 'Value',
                ],
                [
                ],
                [
                    'max' => 5,
                ],
                [
                    'max' => null,
                ],
                [
                    'min' => 5,
                ],
                [
                    'min' => null,
                ],
                [
                    'min' => null,
                    'max' => 5,
                ],
                [
                    'min' => 5,
                    'max' => null,
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $required, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition(['min' => 5, 'max' => 10], $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\RangeType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        $this->assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    public function emptyProvider(): array
    {
        return [
            [null, true],
            [42, false],
            [42.5, false],
            [0, false],
        ];
    }
}
