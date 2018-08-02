<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\BooleanType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class BooleanTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp(): void
    {
        $this->type = new BooleanType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\BooleanType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('boolean', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\BooleanType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\BooleanType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\BooleanType::configureOptions
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
            [[], true, null, false],
            [[], false, null, null],
            [[], true, false, false],
            [[], false, false, false],
            [[], true, true, true],
            [[], false, true, true],
        ];
    }

    public function validOptionsProvider(): array
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

    public function invalidOptionsProvider(): array
    {
        return [
            [
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\BooleanType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\BooleanType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $required, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition([], $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
    {
        return [
            ['12', false, false],
            [12.3, false, false],
            [true, false, true],
            [false, false, true],
            [null, false, true],
            [true, true, true],
            [false, true, true],
            [null, true, false],
            [[], false, false],
            [12, false, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\BooleanType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        $this->assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    public function emptyProvider(): array
    {
        return [
            [null, true],
            [false, false],
            [true, false],
        ];
    }
}
