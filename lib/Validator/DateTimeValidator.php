<?php

namespace Netgen\BlockManager\Validator;

use DateTimeInterface;
use DateTimeZone;
use Netgen\BlockManager\Parameters\ParameterType\DateTimeType;
use Netgen\BlockManager\Validator\Constraint\DateTime;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided DateTimeInterface object is a valid value (that is, if it has
 * a timezone with type === 3 (e.g. Europe/Zagreb)) or if the provided array has a datetime and
 * and timezone values in the accepted formats.
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

        if (!$value instanceof DateTimeInterface && !is_array($value)) {
            throw new UnexpectedTypeException($value, sprintf('%s or array', DateTimeInterface::class));
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        if (is_array($value)) {
            $validator->atPath('datetime')->validate(
                isset($value['datetime']) ? $value['datetime'] : '',
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                    new Constraints\DateTime(Kernel::VERSION_ID >= 30100 ? ['format' => DateTimeType::DATE_FORMAT] : []),
                ]
            );
        }

        $timeZone = $value instanceof DateTimeInterface ?
            $value->getTimezone()->getName() :
            (isset($value['timezone']) ? $value['timezone'] : '');

        $validator->atPath('timezone')->validate(
            $timeZone,
            [
                new Constraints\Type(['type' => 'string']),
                new Constraints\Callback(
                    [
                        'callback' => function ($timeZoneName, ExecutionContextInterface $context) use ($constraint) {
                            if (in_array($timeZoneName, DateTimeZone::listIdentifiers(), true)) {
                                return;
                            }

                            $context->buildViolation($constraint->invalidTimeZoneMessage)
                                ->setParameter('%timeZone%', $timeZoneName)
                                ->addViolation();
                        },
                    ]
                ),
            ]
        );
    }
}
