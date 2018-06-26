<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Utils;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\BlockManager\Utils\DateTimeUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

final class DateTimeUtilsTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestamp(): void
    {
        // Friday March 23, 2018 21:13:20, Antarctica/Casey
        ClockMock::withClockMock(1521800000);

        $dateTime = DateTimeUtils::createFromTimestamp();

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertSame(1521800000, $dateTime->getTimestamp());
        $this->assertSame(date_default_timezone_get(), $dateTime->getTimezone()->getName());

        ClockMock::withClockMock(false);
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestampWithTimestamp(): void
    {
        $dateTime = DateTimeUtils::createFromTimestamp(123);

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertSame(123, $dateTime->getTimestamp());
        $this->assertSame(date_default_timezone_get(), $dateTime->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestampWithTimestampAndTimeZone(): void
    {
        $dateTime = DateTimeUtils::createFromTimestamp(123, 'Antarctica/Casey');

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertSame(123, $dateTime->getTimestamp());
        $this->assertSame('Antarctica/Casey', $dateTime->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::isBetweenDates
     * @dataProvider isBetweenDatesProvider
     */
    public function testIsBetweenDates(?DateTimeInterface $from = null, ?DateTimeInterface $to = null, bool $result = false): void
    {
        $this->assertSame($result, DateTimeUtils::isBetweenDates(new DateTimeImmutable('@15000'), $from, $to));
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
            $this->assertNull($dateTime);

            return;
        }

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertSame($input['timezone'], $dateTime->getTimezone()->getName());
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

        $this->assertInternalType('array', $timeZones);
        $this->assertNotEmpty($timeZones);

        foreach ($timeZones as $continent => $innerTimeZones) {
            $this->assertInternalType('string', $continent);
            $this->assertInternalType('array', $innerTimeZones);
            $this->assertNotEmpty($innerTimeZones);
        }
    }
}
