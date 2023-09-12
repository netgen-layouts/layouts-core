<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\ConditionType;

use Netgen\Layouts\Layout\Resolver\ConditionType\Time;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class TimeTest extends TestCase
{
    private Time $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new Time();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Time::getType
     */
    public function testGetType(): void
    {
        self::assertSame('time', $this->conditionType::getType());
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Time::getConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Time::matches
     *
     * @param mixed $value
     *
     * @dataProvider matchesDataProvider
     */
    public function testMatches($value, bool $matches): void
    {
        // Friday March 23, 2018 21:13:20, Antarctica/Casey
        ClockMock::withClockMock(1_521_800_000);

        self::assertSame($matches, $this->conditionType->matches(Request::create('/'), $value));

        ClockMock::withClockMock(false);
    }

    public static function validationDataProvider(): iterable
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
            [null, false],
        ];
    }

    public static function matchesDataProvider(): iterable
    {
        return [
            [['from' => [], 'to' => []], true],
            [['from' => null, 'to' => []], true],
            [['from' => [], 'to' => null], true],
            [['from' => null, 'to' => null], true],
            [['from' => []], true],
            [['from' => null], true],
            [['to' => []], true],
            [['to' => null], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => []], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => null], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], true],
            [['from' => ['datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => []], false],
            [['from' => ['datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => null], false],
            [['from' => ['datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => [], 'to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => null, 'to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => [], 'to' => ['datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey']], true],
            [['from' => null, 'to' => ['datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey']], true],
            [['to' => ['datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey']], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => ['datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey']], true],
            [['from' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => ['datetime' => '2018-03-21 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => ['datetime' => '2018-03-24 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => ['datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [['from' => ['datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey'], 'to' => ['datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey']], false],
            [[], true],
            ['not_array', false],
        ];
    }
}
