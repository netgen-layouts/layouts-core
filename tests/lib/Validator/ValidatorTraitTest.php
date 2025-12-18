<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Exception;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCaseTrait;
use Netgen\Layouts\Tests\Validator\Stubs\ValueValidator;
use Netgen\Layouts\Validator\ValidatorTrait;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversTrait(ValidatorTrait::class)]
final class ValidatorTraitTest extends TestCase
{
    use ValidatorTestCaseTrait;

    private ValueValidator $valueValidator;

    protected function setUp(): void
    {
        $this->valueValidator = new ValueValidator();
        $this->valueValidator->setValidator($this->createValidator());
    }

    #[DataProvider('validateIdentifierDataProvider')]
    public function testValidateIdentifier(mixed $identifier, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $this->valueValidator->validateIdentifier($identifier);
    }

    public function testValidateIdentifierThrowsValidationExceptionOnValidationError(): void
    {
        $this->expectException(ValidationException::class);

        $validatorStub = self::createStub(ValidatorInterface::class);
        $validatorStub
            ->method('validate')
            ->willThrowException(new Exception());

        $this->valueValidator->setValidator($validatorStub);
        $this->valueValidator->validateIdentifier('identifier');
    }

    #[DataProvider('validatePositionDataProvider')]
    public function testValidatePosition(mixed $position, bool $isRequired, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $this->valueValidator->validatePosition($position, null, $isRequired);
    }

    #[DoesNotPerformAssertions]
    public function testValidatePositionWithDefaultRequiredValue(): void
    {
        $this->valueValidator->validatePosition(null);
    }

    #[DataProvider('validateLocaleDataProvider')]
    public function testValidateLocale(string $locale, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $this->valueValidator->validateLocale($locale);
    }

    public static function validateIdDataProvider(): iterable
    {
        return [
            [24, true],
            ['24', true],
            ['', false],
            [[], false],
            [null, false],
        ];
    }

    public static function validateIdentifierDataProvider(): iterable
    {
        return [
            ['a', true],
            ['identifier', true],
            ['identifier_2', true],
            ['345identifier', true],
            ['345_identifier', true],
            ['other identifier', false],
            ['345', false],
            ['345_678', false],
            ['___', false],
            ['', false],
        ];
    }

    public static function validatePositionDataProvider(): iterable
    {
        return [
            [-5, false, false],
            [-5, true, false],
            [-1, false, false],
            [-1, true, false],
            [0, false, true],
            [0, true, true],
            [24, false, true],
            [24, true, true],
            [null, false, true],
            [null, true, false],
        ];
    }

    public static function validateOffsetAndLimitDataProvider(): iterable
    {
        return [
            [0, null, true],
            [5, null, true],
            [null, null, false],
            [0, 1, true],
            [5, 1, true],
            [null, 1, false],
        ];
    }

    public static function validateLocaleDataProvider(): iterable
    {
        return [
            ['en', true],
            ['en_US', true],
            ['pt', true],
            ['pt_PT', true],
            ['zh_Hans', true],
            ['fil_PH', true],
            // We do not allow non-canonicalized locales
            ['en-US', false],
            ['es-AR', false],
            ['fr_FR.utf8', false],
            ['EN', false],
            // Invalid locales
            ['foobar', false],
        ];
    }
}
