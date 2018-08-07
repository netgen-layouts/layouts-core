<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\BlockManager\Validator\LocaleValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class LocaleValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $this->constraint = new LocaleConstraint();

        parent::setUp();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        return new LocaleValidator();
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LocaleValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $value, bool $isValid): void
    {
        self::assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LocaleValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Locale", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        self::assertValid(true, 'hr_HR');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LocaleValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        self::assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            [null, true],
            ['', true],
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
