<?php

namespace Netgen\BlockManager\Tests\Validator;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\DateTime as DateTimeConstraint;
use Netgen\BlockManager\Validator\DateTimeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class DateTimeValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new DateTimeConstraint();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new DateTimeValidator();
    }

    /**
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\DateTime", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new DateTimeImmutable());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "DateTimeInterface or array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
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
}
