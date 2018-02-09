<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Validator\Constraint\Parameters\DateTime as DateTimeConstraint;

/**
 * Parameter type used to store and validate a date and time value. The value of the parameter
 * is a standard \DateTimeInterface object from PHP, however, do note that you can only store
 * the objects which have a time zone type === 3 (meaning, timezone specified via identifier
 * like Europe/Zagreb).
 */
final class DateTimeType extends ParameterType
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    public function getIdentifier()
    {
        return 'datetime';
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return !$value instanceof DateTimeInterface;
    }

    public function toHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if (!$value instanceof DateTimeInterface && !is_array($value)) {
            return null;
        }

        if (is_array($value) && (empty($value['datetime']) || empty($value['timezone']))) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return array(
                'datetime' => $value->format(static::DATE_FORMAT),
                'timezone' => $value->getTimezone()->getName(),
            );
        }

        return array(
            'datetime' => $value['datetime'],
            'timezone' => $value['timezone'],
        );
    }

    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if (!is_array($value) || empty($value['datetime']) || empty($value['timezone'])) {
            return null;
        }

        return new DateTimeImmutable($value['datetime'], new DateTimeZone($value['timezone']));
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return array(
            new DateTimeConstraint(),
        );
    }
}
