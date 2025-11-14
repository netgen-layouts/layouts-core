<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\EnumType;
use Netgen\Layouts\Tests\Parameters\Stubs\EnumStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

#[CoversClass(EnumType::class)]
final class EnumTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new EnumType();
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('enum', $this->type::getIdentifier());
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

    /**
     * Provider for testing default parameter values.
     */
    public static function defaultValueDataProvider(): iterable
    {
        return [
            [['class' => EnumStub::class], true, null, EnumStub::Foo],
            [['class' => EnumStub::class], false, null, null],
            [['class' => EnumStub::class], true, 'bar', 'bar'],
            [['class' => EnumStub::class], false, 'bar', 'bar'],
        ];
    }

    /**
     * Provider for testing valid parameter attributes.
     */
    public static function validOptionsDataProvider(): iterable
    {
        return [
            [
                [
                    'class' => EnumStub::class,
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'class' => EnumStub::class,
                ],
            ],
            [
                [
                    'multiple' => false,
                    'class' => EnumStub::class,
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'class' => EnumStub::class,
                ],
            ],
            [
                [
                    'multiple' => true,
                    'class' => EnumStub::class,
                ],
                [
                    'multiple' => true,
                    'expanded' => false,
                    'class' => EnumStub::class,
                ],
            ],
            [
                [
                    'expanded' => false,
                    'class' => EnumStub::class,
                ],
                [
                    'multiple' => false,
                    'expanded' => false,
                    'class' => EnumStub::class,
                ],
            ],
            [
                [
                    'expanded' => true,
                    'class' => EnumStub::class,
                ],
                [
                    'multiple' => false,
                    'expanded' => true,
                    'class' => EnumStub::class,
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
                    'class' => EnumStub::class,
                    'multiple' => 'true',
                ],
            ],
            [
                [
                    'class' => EnumStub::class,
                    'expanded' => 'true',
                ],
            ],
            [
                [
                    'class' => null,
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

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $isRequired, bool $isMultiple, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition(['class' => EnumStub::class, 'multiple' => $isMultiple], $isRequired);
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
            [EnumStub::Foo, false, false, true],
            [[EnumStub::Foo], false, false, false],
            ['foo', false, false, false],
            [['foo'], false, false, false],
            [EnumStub::Bar, false, false, true],
            [[EnumStub::Bar], false, false, false],
            ['bar', false, false, false],
            [['bar'], false, false, false],
            [EnumStub::Baz, false, false, true],
            [[EnumStub::Baz], false, false, false],
            ['baz', false, false, false],
            [['baz'], false, false, false],
            ['bat', false, false, false],
            [['bat'], false, false, false],
            [null, false, false, true],
            [[null], false, false, false],
            ['null', false, false, false],
            [['null'], false, false, false],
            [[], false, false, false],
            [null, true, false, false],
            [[null], true, false, false],
            ['null', true, false, false],
            [['null'], true, false, false],
            [[], true, false, false],

            [EnumStub::Foo, false, true, false],
            [[EnumStub::Foo], false, true, true],
            ['foo', false, true, false],
            [['foo'], false, true, false],
            [EnumStub::Bar, false, true, false],
            [[EnumStub::Bar], false, true, true],
            ['bar', false, true, false],
            [['bar'], false, true, false],
            [EnumStub::Baz, false, true, false],
            [[EnumStub::Baz], false, true, true],
            ['baz', false, true, false],
            [['baz'], false, true, false],
            ['bat', false, true, false],
            [['bat'], false, true, false],
            [null, false, true, true],
            [[null], false, true, false],
            ['null', false, true, false],
            [['null'], false, true, false],
            [[], false, true, true],
            [null, true, true, false],
            [[null], true, true, false],
            ['null', true, true, false],
            [['null'], true, true, false],
            [[], true, true, false],
        ];
    }

    #[DataProvider('fromHashDataProvider')]
    public function testFromHash(mixed $value, mixed $convertedValue, bool $multiple): void
    {
        self::assertSame(
            $convertedValue,
            $this->type->fromHash(
                $this->getParameterDefinition(
                    [
                        'class' => EnumStub::class,
                        'multiple' => $multiple,
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
                'foo',
                EnumStub::Foo,
                false,
            ],
            [
                ['foo', 'bar'],
                EnumStub::Foo,
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
                'foo',
                [EnumStub::Foo],
                true,
            ],
            [
                ['foo', 'bar'],
                [EnumStub::Foo, EnumStub::Bar],
                true,
            ],
        ];
    }

    #[DataProvider('toHashDataProvider')]
    public function testToHash(mixed $value, mixed $convertedValue, bool $multiple): void
    {
        self::assertSame(
            $convertedValue,
            $this->type->toHash(
                $this->getParameterDefinition(
                    [
                        'class' => EnumStub::class,
                        'multiple' => $multiple,
                    ],
                ),
                $value,
            ),
        );
    }

    public static function toHashDataProvider(): iterable
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
                EnumStub::Foo,
                'foo',
                false,
            ],
            [
                [EnumStub::Foo, EnumStub::Bar],
                'foo',
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
                EnumStub::Foo,
                ['foo'],
                true,
            ],
            [
                [EnumStub::Foo, EnumStub::Bar],
                ['foo', 'bar'],
                true,
            ],
        ];
    }

    #[DataProvider('emptyDataProvider')]
    public function testIsValueEmpty(mixed $value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    public static function emptyDataProvider(): iterable
    {
        return [
            [null, true],
            [[], true],
            [EnumStub::Foo, false],
            [[EnumStub::Foo], false],
            ['foo', false],
            [['foo'], false],
            ['bat', false],
            [['bat'], false],
            ['', false],
            [false, false],
        ];
    }
}
