<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterType\IdentifierType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

#[CoversClass(IdentifierType::class)]
final class IdentifierTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new IdentifierType();
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('identifier', $this->type::getIdentifier());
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     */
    #[DataProvider('validOptionsDataProvider')]
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameterDefinition = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameterDefinition->options);
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
     * @return iterable<mixed>
     */
    public static function validOptionsDataProvider(): iterable
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function invalidOptionsDataProvider(): iterable
    {
        return [
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

    /**
     * @return iterable<mixed>
     */
    public static function validationDataProvider(): iterable
    {
        return [
            ['a123abcASD', true, true],
            ['a123abc_ASD', true, true],
            ['a123abcASD', false, true],
            ['a123abc_ASD', false, true],
            [null, false, true],
            [null, true, false],
            ['a123abc ASD', false, false],
            ['a123a-bcASD', false, false],
            ['a123abc.ASD', false, false],
            ['123abcASD', false, false],
        ];
    }

    #[DataProvider('emptyDataProvider')]
    public function testIsValueEmpty(mixed $value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    /**
     * @return iterable<mixed>
     */
    public static function emptyDataProvider(): iterable
    {
        return [
            [null, true],
            ['foo', false],
            ['', true],
        ];
    }
}
