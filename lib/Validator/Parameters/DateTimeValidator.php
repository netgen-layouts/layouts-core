<?php

namespace Netgen\BlockManager\Validator\Parameters;

use DateTimeZone;
use Netgen\BlockManager\Parameters\ParameterType\DateTimeType;
use Netgen\BlockManager\Parameters\Value\DateTimeValue;
use Netgen\BlockManager\Validator\Constraint\Parameters\DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is a valid instance of
 * \Netgen\BlockManager\Parameters\Value\DateTimeValue object.
 */
final class DateTimeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof DateTime) {
            throw new UnexpectedTypeException($constraint, DateTime::class);
        }

        if (!$value instanceof DateTimeValue) {
            throw new UnexpectedTypeException($value, DateTimeValue::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $dateTime = $value->getDateTime();

        if ($dateTime !== null) {
            $validator->atPath('dateTime')->validate(
                $dateTime,
                array(
                    new Constraints\Type(array('type' => 'string')),
                    new Constraints\DateTime(array('format' => DateTimeType::DATE_FORMAT)),
                )
            );
        }

        $timeZoneConstraints = array(
            new Constraints\Type(array('type' => 'string')),
            new Constraints\Callback(
                array(
                    'callback' => function ($timeZone, ExecutionContextInterface $context) use ($constraint) {
                        if (is_string($timeZone) && !in_array($timeZone, DateTimeZone::listIdentifiers(), true)) {
                            $context->buildViolation($constraint->invalidTimeZoneMessage)
                                ->setParameter('%timeZone%', $timeZone)
                                ->addViolation();
                        }
                    },
                )
            ),
        );

        if ($dateTime !== null) {
            array_unshift($timeZoneConstraints, new Constraints\NotNull());
        }

        $validator->atPath('timeZone')->validate($value->getTimeZone(), $timeZoneConstraints);
    }
}
