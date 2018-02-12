<?php

namespace Netgen\BlockManager\Tests\Validator\Parameters;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Parameters\DateTime as DateTimeConstraint;
use Netgen\BlockManager\Validator\Parameters\DateTimeValidator;
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
     * @covers \Netgen\BlockManager\Validator\Parameters\DateTimeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Parameters\DateTime", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new DateTimeImmutable());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\DateTimeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "DateTimeInterface or array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array(null, true),
            array(new DateTime(), true),
            array(new DateTimeImmutable(), true),
            array(new DateTime('now', new DateTimeZone('Antarctica/Casey')), true),
            array(new DateTimeImmutable('now', new DateTimeZone('Antarctica/Casey')), true),
            array(new DateTime('now', new DateTimeZone('+01:00')), false),
            array(new DateTimeImmutable('now', new DateTimeZone('+01:00')), false),
            array(new DateTime('now', new DateTimeZone('CAST')), false),
            array(new DateTimeImmutable('now', new DateTimeZone('CAST')), false),
            array(array('datetime' => '2018-02-01 00:00:00'), false),
            array(array('timezone' => 'Antarctica/Casey'), false),
            array(array('datetime' => '2018-02-01 00:00:00', 'timezone' => ''), false),
            array(array('datetime' => '', 'timezone' => 'Antarctica/Casey'), false),
            array(array('datetime' => '', 'timezone' => ''), false),
            array(array('datetime' => '2018-02-01 15:00:00', 'timezone' => '+01:00'), false),
            array(array('datetime' => '2018-02-01 15:00:00', 'timezone' => 'CAST'), false),
            array(array('datetime' => '2018-02-01 15:00:00', 'timezone' => 'Antarctica/Casey'), true),
        );
    }
}
