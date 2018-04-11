<?php

namespace Netgen\BlockManager\Utils;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

final class DateTimeUtils
{
    /**
     * Creates a new \DateTimeImmutable instance from provided timestamp and timezone identifiers.
     *
     * Current timestamp and timezones are used if not provided.
     *
     * @param int $timestamp
     * @param string $timeZone
     *
     * @return \DateTimeImmutable
     */
    public static function createFromTimestamp($timestamp = null, $timeZone = null)
    {
        $timeZone = is_string($timeZone) ? new DateTimeZone($timeZone) : null;
        $timestamp = is_int($timestamp) ? $timestamp : time();

        $dateTime = new DateTimeImmutable('now', $timeZone);

        return $dateTime->setTimestamp($timestamp);
    }

    /**
     * Creates a new \DateTimeImmutable instance from provided array.
     *
     * The array needs to contain two keys with string values:
     * 1) "datetime" - for date & time in one of the valid formats
     * 2) "timezone" - a valid timezone identifier
     *
     * Returns null if provided array is not of valid format.
     *
     * @param array $datetime
     *
     * @return \DateTimeImmutable|null
     */
    public static function createFromArray(array $datetime)
    {
        if (empty($datetime['datetime']) || !is_string($datetime['datetime'])) {
            return null;
        }

        if (empty($datetime['timezone']) || !is_string($datetime['timezone'])) {
            return null;
        }

        return new DateTimeImmutable($datetime['datetime'], new DateTimeZone($datetime['timezone']));
    }

    /**
     * Returns if the provided DateTime instance is between the provided dates.
     *
     * @param \DateTimeInterface $date
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return bool
     */
    public static function isBetweenDates(DateTimeInterface $date = null, DateTimeInterface $from = null, DateTimeInterface $to = null)
    {
        $date = $date ?: self::createFromTimestamp();

        if ($from instanceof DateTimeInterface && $date < $from) {
            return false;
        }

        if ($to instanceof DateTimeInterface && $date > $to) {
            return false;
        }

        return true;
    }

    /**
     * Returns the formatted list of all timezones, separated by regions.
     *
     * @return array
     */
    public static function getTimeZoneList()
    {
        $timeZoneList = array();
        foreach (DateTimeZone::listIdentifiers() as $timeZone) {
            list($region, $name) = self::parseTimeZone($timeZone);

            $offset = self::buildOffsetString($timeZone);
            $name = sprintf('%s (%s)', str_replace('_', ' ', $name), $offset);

            $timeZoneList[$region][$name] = $timeZone;
        }

        return $timeZoneList;
    }

    /**
     * Returns the array with human readable region and timezone name for the provided
     * timezone identifier.
     *
     * @param string $timeZone
     *
     * @return array
     */
    private static function parseTimeZone($timeZone)
    {
        $parts = explode('/', $timeZone);

        if (count($parts) > 2) {
            return array($parts[0], $parts[1] . ' / ' . $parts[2]);
        } elseif (count($parts) > 1) {
            return array($parts[0], $parts[1]);
        }

        return array('Other', $parts[0]);
    }

    /**
     * Returns the formatted UTC offset for the provided timezone identifier
     * in the form of (+/-)HH:mm.
     *
     * @param string $timeZone
     *
     * @return string
     */
    private static function buildOffsetString($timeZone)
    {
        $offset = self::createFromTimestamp(null, $timeZone)->getOffset();

        $hours = intdiv($offset, 3600);
        $minutes = (int) (($offset % 3600) / 60);

        return sprintf('%s%02d:%02d', $offset >= 0 ? '+' : '-', abs($hours), abs($minutes));
    }
}
