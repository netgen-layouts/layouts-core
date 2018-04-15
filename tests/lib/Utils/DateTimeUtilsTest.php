<?php

namespace Netgen\BlockManager\Tests\Utils;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Netgen\BlockManager\Utils\DateTimeUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

final class DateTimeUtilsTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        ClockMock::register(DateTimeUtils::class);
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestamp()
    {
        // Friday March 23, 2018 21:13:20, Antarctica/Casey
        ClockMock::withClockMock(1521800000);

        $dateTime = DateTimeUtils::createFromTimestamp();

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertEquals(1521800000, $dateTime->getTimestamp());
        $this->assertEquals(date_default_timezone_get(), $dateTime->getTimezone()->getName());

        ClockMock::withClockMock(false);
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestampWithTimestamp()
    {
        $dateTime = DateTimeUtils::createFromTimestamp(123);

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertEquals(123, $dateTime->getTimestamp());
        $this->assertEquals(date_default_timezone_get(), $dateTime->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromTimestamp
     */
    public function testCreateFromTimestampWithTimestampAndTimeZone()
    {
        $dateTime = DateTimeUtils::createFromTimestamp(123, 'Antarctica/Casey');

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertEquals(123, $dateTime->getTimestamp());
        $this->assertEquals('Antarctica/Casey', $dateTime->getTimezone()->getName());
    }

    /**
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     * @param bool $result
     *
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::isBetweenDates
     * @dataProvider isBetweenDatesProvider
     */
    public function testIsBetweenDates(DateTimeInterface $from = null, DateTimeInterface $to = null, $result = false)
    {
        $this->assertEquals($result, DateTimeUtils::isBetweenDates(new DateTime('@15000'), $from, $to));
    }

    public function isBetweenDatesProvider()
    {
        return array(
            array(new DateTime('@10000'), new DateTime('@20000'), true),
            array(new DateTime('@17000'), new DateTime('@20000'), false),
            array(new DateTime('@10000'), new DateTime('@13000'), false),
            array(new DateTime('@10000'), new DateTime('@15000'), true),
            array(new DateTime('@15000'), new DateTime('@20000'), true),
            array(new DateTime('@20000'), new DateTime('@10000'), false),
            array(new DateTime('@20000'), new DateTime('@17000'), false),
            array(new DateTime('@13000'), new DateTime('@10000'), false),
            array(new DateTime('@15000'), new DateTime('@10000'), false),
            array(new DateTime('@20000'), new DateTime('@15000'), false),
            array(null, new DateTime('@20000'), true),
            array(null, new DateTime('@15000'), true),
            array(null, new DateTime('@10000'), false),
            array(new DateTime('@10000'), null, true),
            array(new DateTime('@15000'), null, true),
            array(new DateTime('@20000'), null, false),
            array(null, null, true),
        );
    }

    /**
     * @param array $input
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::createFromArray
     * @dataProvider createFromArrayProvider
     */
    public function testCreateFromArray(array $input, $isValid)
    {
        $dateTime = DateTimeUtils::createFromArray($input);

        if (!$isValid) {
            $this->assertNull($dateTime);

            return;
        }

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertEquals($input['timezone'], $dateTime->getTimezone()->getName());
    }

    public function createFromArrayProvider()
    {
        return array(
            array(array('datetime' => '2018-03-31 11:00:00', 'timezone' => 'Antarctica/Casey'), true),
            array(array('datetime' => '2018-03-31 11:00:00'), false),
            array(array('datetime' => '2018-03-31 11:00:00', 'timezone' => ''), false),
            array(array('datetime' => '2018-03-31 11:00:00', 'timezone' => 42), false),
            array(array('timezone' => 'Antarctica/Casey'), false),
            array(array('timezone' => 'Antarctica/Casey', 'datetime' => ''), false),
            array(array('timezone' => 'Antarctica/Casey', 'datetime' => 42), false),
            array(array(), false),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::buildOffsetString
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::getTimeZoneList
     * @covers \Netgen\BlockManager\Utils\DateTimeUtils::parseTimeZone
     */
    public function testGetTimeZoneList()
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
