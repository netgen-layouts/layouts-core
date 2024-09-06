<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\ConditionType;

use DateTimeImmutable;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\ConditionType\TimeValidator;
use Netgen\Layouts\Validator\Constraint\ConditionType\Time;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TimeValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new Time();

        parent::setUp();
    }

    /**
     * @param mixed[]|null $value
     *
     * @covers \Netgen\Layouts\Validator\ConditionType\TimeValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(?array $value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\ConditionType\TimeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\ConditionType\Time", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new DateTimeImmutable());
    }

    /**
     * @covers \Netgen\Layouts\Validator\ConditionType\TimeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "array", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            [['from' => [], 'to' => []], false],
            [['from' => null, 'to' => []], false],
            [['from' => [], 'to' => null], false],
            [['from' => null, 'to' => null], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => null], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => []], false],
            [['from' => ['invalid'], 'to' => null], false],
            [['from' => ['invalid'], 'to' => []], false],
            [['from' => null, 'to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], true],
            [['from' => [], 'to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => null, 'to' => ['invalid']], false],
            [['from' => [], 'to' => ['invalid']], false],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => ['datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey']], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => ['datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => ['datetime' => '2018-03-25 12:00:00', 'timezone' => 'Europe/London'], 'to' => ['datetime' => '2018-03-25 12:59:00', 'timezone' => 'Europe/Zagreb']], false],
            [['from' => ['datetime' => '2018-03-25 13:00:00', 'timezone' => 'Europe/Zagreb'], 'to' => ['datetime' => '2018-03-25 12:01:00', 'timezone' => 'Europe/London']], true],
            [['from' => []], false],
            [['from' => null], false],
            [['to' => []], false],
            [['to' => null], false],
            [[], false],
            [null, true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new TimeValidator();
    }
}
