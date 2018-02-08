<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Value\DateTimeValue;
use Netgen\BlockManager\Validator\Constraint\Parameters\DateTime as DateTimeConstraint;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a date and time value.
 */
final class DateTimeType extends ParameterType
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s';

    public function getIdentifier()
    {
        return 'datetime';
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return !$value instanceof DateTimeValue || empty($value->getDateTime());
    }

    public function toHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if (!$value instanceof DateTimeValue) {
            return null;
        }

        return array(
            'datetime' => $value->getDateTime(),
            'timezone' => $value->getDateTime() !== null ? $value->getTimeZone() : null,
        );
    }

    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if (!is_array($value) || empty($value['datetime'])) {
            return new DateTimeValue();
        }

        return new DateTimeValue(
            array(
                'dateTime' => $value['datetime'],
                'timeZone' => isset($value['timezone']) ? $value['timezone'] : date_default_timezone_get(),
            )
        );
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return array(
            new Constraints\Type(array('type' => DateTimeValue::class)),
            new DateTimeConstraint(),
        );
    }
}
