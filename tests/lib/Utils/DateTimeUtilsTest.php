<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\Layouts\Utils\DateTimeUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

use function date_default_timezone_get;

final class DateTimeUtilsTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::create
     */
    public function testCreate(): void
    {
        // Friday March 23, 2018 21:13:20, Antarctica/Casey
        ClockMock::withClockMock(1_521_800_000);

        $dateTime = DateTimeUtils::create();

        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame(1_521_800_000, $dateTime->getTimestamp());
        self::assertSame(date_default_timezone_get(), $dateTime->getTimezone()->getName());

        ClockMock::withClockMock(false);
    }

    /**
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::create
     */
    public function testCreateWithTimestamp(): void
    {
        $dateTime = DateTimeUtils::create(123);

        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame(123, $dateTime->getTimestamp());
        self::assertSame(date_default_timezone_get(), $dateTime->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::create
     */
    public function testCreateWithTimestampAndTimeZone(): void
    {
        $dateTime = DateTimeUtils::create(123, 'Antarctica/Casey');

        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame(123, $dateTime->getTimestamp());
        self::assertSame('Antarctica/Casey', $dateTime->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::isBetweenDates
     *
     * @dataProvider isBetweenDatesDataProvider
     */
    public function testIsBetweenDates(?DateTimeInterface $from = null, ?DateTimeInterface $to = null, bool $result = false): void
    {
        self::assertSame($result, DateTimeUtils::isBetweenDates(new DateTimeImmutable('@15000'), $from, $to));
    }

    public static function isBetweenDatesDataProvider(): iterable
    {
        return [
            [new DateTimeImmutable('@10000'), new DateTimeImmutable('@20000'), true],
            [new DateTimeImmutable('@17000'), new DateTimeImmutable('@20000'), false],
            [new DateTimeImmutable('@10000'), new DateTimeImmutable('@13000'), false],
            [new DateTimeImmutable('@10000'), new DateTimeImmutable('@15000'), true],
            [new DateTimeImmutable('@15000'), new DateTimeImmutable('@20000'), true],
            [new DateTimeImmutable('@20000'), new DateTimeImmutable('@10000'), false],
            [new DateTimeImmutable('@20000'), new DateTimeImmutable('@17000'), false],
            [new DateTimeImmutable('@13000'), new DateTimeImmutable('@10000'), false],
            [new DateTimeImmutable('@15000'), new DateTimeImmutable('@10000'), false],
            [new DateTimeImmutable('@20000'), new DateTimeImmutable('@15000'), false],
            [null, new DateTimeImmutable('@20000'), true],
            [null, new DateTimeImmutable('@15000'), true],
            [null, new DateTimeImmutable('@10000'), false],
            [new DateTimeImmutable('@10000'), null, true],
            [new DateTimeImmutable('@15000'), null, true],
            [new DateTimeImmutable('@20000'), null, false],
            [null, null, true],
        ];
    }

    /**
     * @param mixed[] $input
     *
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::createFromArray
     *
     * @dataProvider createFromArrayDataProvider
     */
    public function testCreateFromArray(array $input, bool $isValid): void
    {
        $dateTime = DateTimeUtils::createFromArray($input);

        if (!$isValid) {
            self::assertNull($dateTime);

            return;
        }

        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame($input['timezone'], $dateTime->getTimezone()->getName());
    }

    public static function createFromArrayDataProvider(): iterable
    {
        return [
            [['datetime' => '2018-03-31 11:00:00', 'timezone' => 'Antarctica/Casey'], true],
            [['datetime' => '2018-03-31 11:00:00'], false],
            [['datetime' => '2018-03-31 11:00:00', 'timezone' => ''], false],
            [['datetime' => '2018-03-31 11:00:00', 'timezone' => 42], false],
            [['timezone' => 'Antarctica/Casey'], false],
            [['timezone' => 'Antarctica/Casey', 'datetime' => ''], false],
            [['timezone' => 'Antarctica/Casey', 'datetime' => 42], false],
            [[], false],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::buildOffsetString
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::getTimeZoneList
     * @covers \Netgen\Layouts\Utils\DateTimeUtils::parseTimeZone
     */
    public function testGetTimeZoneList(): void
    {
        $timeZones = DateTimeUtils::getTimeZoneList();

        self::assertNotEmpty($timeZones);

        foreach ($timeZones as $innerTimeZones) {
            self::assertNotEmpty($innerTimeZones);
        }
    }
}
