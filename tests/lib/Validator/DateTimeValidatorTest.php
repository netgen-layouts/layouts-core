<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\DateTime as DateTimeConstraint;
use Netgen\Layouts\Validator\DateTimeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DateTimeValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new DateTimeConstraint();
        $this->constraint->allowArray = true;

        parent::setUp();
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Validator\DateTimeValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\DateTimeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\DateTime", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new DateTimeImmutable());
    }

    /**
     * @covers \Netgen\Layouts\Validator\DateTimeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "DateTimeInterface or array", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Validator\DateTimeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValueAndDisabledArray(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "DateTimeInterface", "int(eger)?" given$/');

        $this->constraint->allowArray = false;

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Validator\DateTimeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithArrayValueAndDisabledArray(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "DateTimeInterface", "array" given');

        $this->constraint->allowArray = false;

        $this->assertValid(true, []);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            [null, true],
            [new DateTimeImmutable(), true],
            [new DateTimeImmutable(), true],
            [new DateTimeImmutable('now', new DateTimeZone('Antarctica/Casey')), true],
            [new DateTimeImmutable('now', new DateTimeZone('Antarctica/Casey')), true],
            [new DateTimeImmutable('now', new DateTimeZone('+01:00')), false],
            [new DateTimeImmutable('now', new DateTimeZone('+01:00')), false],
            [new DateTimeImmutable('now', new DateTimeZone('CAST')), false],
            [new DateTimeImmutable('now', new DateTimeZone('CAST')), false],
            [['datetime' => '2018-02-01 00:00:00'], false],
            [['timezone' => 'Antarctica/Casey'], false],
            [['datetime' => '2018-02-01 00:00:00', 'timezone' => ''], false],
            [['datetime' => '', 'timezone' => 'Antarctica/Casey'], false],
            [['datetime' => '', 'timezone' => ''], false],
            [['datetime' => '2018-02-01 15:00:00', 'timezone' => '+01:00'], false],
            [['datetime' => '2018-02-01 15:00:00', 'timezone' => 'CAST'], false],
            [['datetime' => '2018-02-01 15:00:00', 'timezone' => 'Antarctica/Casey'], true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new DateTimeValidator();
    }
}
