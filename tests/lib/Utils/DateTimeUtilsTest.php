<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Utils;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\BlockManager\Tests\TestCase\LegacyTestCaseTrait;
use Netgen\BlockManager\Utils\DateTimeUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

final class DateTimeUtilsTest extends TestCase
{
    use LegacyTestCaseTrait;

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestamp(): void
    {
        // Friday March 23, 2018 21:13:20, Antarctica/Casey
        ClockMock::withClockMock(1521800000);

        $dateTime = DateTimeUtils::createFromTimestamp();

        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame(1521800000, $dateTime->getTimestamp());
        self::assertSame(date_default_timezone_get(), $dateTime->getTimezone()->getName());

        ClockMock::withClockMock(false);
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestampWithTimestamp(): void
    {
        $dateTime = DateTimeUtils::createFromTimestamp(123);

        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame(123, $dateTime->getTimestamp());
        self::assertSame(date_default_timezone_get(), $dateTime->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestampWithTimestampAndTimeZone(): void
    {
        $dateTime = DateTimeUtils::createFromTimestamp(123, 'Antarctica/Casey');

        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame(123, $dateTime->getTimestamp());
        self::assertSame('Antarctica/Casey', $dateTime->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::isBetweenDates
     * @dataProvider isBetweenDatesProvider
     */
    public function testIsBetweenDates(?DateTimeInterface $from = null, ?DateTimeInterface $to = null, bool $result = false): void
    {
        self::assertSame($result, DateTimeUtils::isBetweenDates(new DateTimeImmutable('@15000'), $from, $to));
    }

    public function isBetweenDatesProvider(): array
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
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromArray
     * @dataProvider createFromArrayProvider
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

    public function createFromArrayProvider(): array
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
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::buildOffsetString
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::getTimeZoneList
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::parseTimeZone
     */
    public function testGetTimeZoneList(): void
    {
        $timeZones = DateTimeUtils::getTimeZoneList();

        self::assertNotEmpty($timeZones);

        foreach ($timeZones as $continent => $innerTimeZones) {
            self::assertIsString($continent);
            self::assertIsArray($innerTimeZones);
            self::assertNotEmpty($innerTimeZones);
        }
    }
}
