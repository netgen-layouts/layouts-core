<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a date and time value.
 */
final class DateTimeType extends ParameterType
{
    public function getIdentifier()
    {
        return 'datetime';
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $value === null;
    }

    public function toHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if (!$value instanceof DateTimeInterface) {
            return null;
        }

        return $value->format(DateTime::RFC3339);
    }

    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if (!is_string($value)) {
            return null;
        }

        $dateTime = DateTimeImmutable::createFromFormat(DateTime::RFC3339, $value);
        if (!$dateTime instanceof DateTimeInterface || $dateTime->format(DateTime::RFC3339) !== $value) {
            return null;
        }

        return $dateTime;
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => DateTimeInterface::class,
                )
            ),
        );
    }
}
