<?php

namespace Netgen\BlockManager\Parameters\Value;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\ValueObject;

final class DateTimeValue extends ValueObject
{
    /**
     * @var string
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $timeZone;

    /**
     * Returns the formatted date and time.
     *
     * @return string
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Returns the timezone identifier.
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Returns the \DateTimeInterface instance representing this value.
     *
     * @return \DateTimeInterface
     */
    public function getDateTimeInstance()
    {
        $timeZone = $this->timeZone !== null ? new DateTimeZone($this->timeZone) : null;

        return $this->dateTime !== null ?
            new DateTimeImmutable($this->dateTime, $timeZone) :
            null;
    }
}
