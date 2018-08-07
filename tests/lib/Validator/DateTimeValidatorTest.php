<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\DateTime as DateTimeConstraint;
use Netgen\BlockManager\Validator\DateTimeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class DateTimeValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $this->constraint = new DateTimeConstraint();
        $this->constraint->allowArray = true;

        parent::setUp();
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\DateTime", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new DateTimeImmutable());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "DateTimeInterface or array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "DateTimeInterface", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValueAndDisabledArray(): void
    {
        $this->constraint->allowArray = false;

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "DateTimeInterface", "array" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithArrayValueAndDisabledArray(): void
    {
        $this->constraint->allowArray = false;

        $this->assertValid(true, []);
    }

    public function validateDataProvider(): array
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
