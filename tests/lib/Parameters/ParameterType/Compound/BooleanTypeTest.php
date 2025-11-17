<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType\Compound;

use Netgen\Layouts\Parameters\ParameterType\Compound\BooleanType;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

#[CoversClass(BooleanType::class)]
final class BooleanTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new BooleanType();
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('compound_boolean', $this->type::getIdentifier());
    }

    /**
     * @param array<string, mixed> $options
     */
    #[DataProvider('defaultValueDataProvider')]
    public function testGetDefaultValue(array $options, bool $required, mixed $defaultValue, mixed $expected): void
    {
        $parameter = $this->getParameterDefinition($options, $required, $defaultValue);
        self::assertSame($expected, $parameter->getDefaultValue());
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     */
    #[DataProvider('validOptionsDataProvider')]
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @param mixed[] $options
     */
    #[DataProvider('invalidOptionsDataProvider')]
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    public static function defaultValueDataProvider(): iterable
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

    public static function validOptionsDataProvider(): iterable
    {
        return [
            [
                [
                    'reverse' => false,
                ],
                [
                    'reverse' => false,
                ],
            ],
            [
                [
                    'reverse' => true,
                ],
                [
                    'reverse' => true,
                ],
            ],
            [
                [],
                [
                    'reverse' => false,
                ],
            ],
        ];
    }

    public static function invalidOptionsDataProvider(): iterable
    {
        return [
            [
                [
                    'reverse' => 'true',
                ],
            ],
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $required, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition([], $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public static function validationDataProvider(): iterable
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

    #[DataProvider('emptyDataProvider')]
    public function testIsValueEmpty(mixed $value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    public static function emptyDataProvider(): iterable
    {
        return [
            [null, true],
            [false, false],
            [true, false],
        ];
    }
}
