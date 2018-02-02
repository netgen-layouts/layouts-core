<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\BlockManager\Parameters\ParameterInterface;
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

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null;
    }

    public function toHash(ParameterInterface $parameter, $value)
    {
        if (!$value instanceof DateTimeInterface) {
            return null;
        }

        return $value->format(DateTimeImmutable::RFC3339);
    }

    public function fromHash(ParameterInterface $parameter, $value)
    {
        if (!is_string($value)) {
            return null;
        }

        $dateTime = DateTimeImmutable::createFromFormat(DateTimeImmutable::RFC3339, $value);
        if (!$dateTime instanceof DateTimeInterface || $dateTime->format(DateTimeImmutable::RFC3339) !== $value) {
            return null;
        }

        return $dateTime;
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
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
