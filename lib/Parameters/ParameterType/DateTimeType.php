<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use DateTimeInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Netgen\BlockManager\Validator\Constraint\DateTime as DateTimeConstraint;

/**
 * Parameter type used to store and validate a date and time value. The value of the parameter
 * is a standard \DateTimeInterface object from PHP, however, do note that you can only store
 * the objects which have a time zone type === 3 (meaning, timezone specified via identifier
 * like Europe/Zagreb).
 */
final class DateTimeType extends ParameterType
{
    private const STORAGE_FORMAT = 'Y-m-d H:i:s.u';

    public function getIdentifier(): string
    {
        return 'datetime';
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return !$value instanceof DateTimeInterface;
    }

    public function toHash(ParameterDefinition $parameterDefinition, $value)
    {
        if (!$value instanceof DateTimeInterface) {
            return null;
        }

        return [
            'datetime' => $value->format(self::STORAGE_FORMAT),
            'timezone' => $value->getTimezone()->getName(),
        ];
    }

    public function fromHash(ParameterDefinition $parameterDefinition, $value)
    {
        return is_array($value) ? DateTimeUtils::createFromArray($value) : null;
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new DateTimeConstraint(),
        ];
    }
}
