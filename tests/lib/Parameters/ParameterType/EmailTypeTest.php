<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterType\EmailType;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

#[CoversClass(EmailType::class)]
final class EmailTypeTest extends TestCase
{
    use ParameterTypeTestTrait;
    use ValidatorTestCaseTrait;

    protected function setUp(): void
    {
        $this->type = new EmailType();
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('email', $this->type::getIdentifier());
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

    public static function validOptionsDataProvider(): iterable
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

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
    public function testValidation(mixed $value, bool $isValid): void
    {
        $validator = $this->createValidator();

        $parameterDefinition = $this->getParameterDefinition();

        $errors = $validator->validate($value, $this->type->getConstraints($parameterDefinition, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public static function validationDataProvider(): iterable
    {
        return [
            [null, true],
            ['info', false],
            ['info@netgen.io', true],
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
            ['info@netgen.io', false],
            ['', true],
        ];
    }
}
