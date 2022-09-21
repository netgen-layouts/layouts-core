<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use DateTimeInterface;
use DateTimeZone;
use Netgen\Layouts\Validator\Constraint\DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function in_array;
use function is_array;
use function sprintf;

/**
 * Validates if the provided DateTimeInterface object is a valid value (that is, if it has
 * a timezone with type === 3 (e.g. Europe/Zagreb)) or if the provided array has a datetime and
 * and timezone values in the accepted formats.
 */
final class DateTimeValidator extends ConstraintValidator
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof DateTime) {
            throw new UnexpectedTypeException($constraint, DateTime::class);
        }

        if (!$value instanceof DateTimeInterface && !is_array($value)) {
            throw new UnexpectedTypeException(
                $value,
                $constraint->allowArray ?
                    sprintf('%s or array', DateTimeInterface::class) :
                    DateTimeInterface::class,
            );
        }

        if (!$constraint->allowArray && is_array($value)) {
            throw new UnexpectedTypeException($value, DateTimeInterface::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        if (is_array($value)) {
            $validator->atPath('datetime')->validate(
                $value['datetime'] ?? '',
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                    new Constraints\DateTime(['format' => self::DATE_FORMAT]),
                ],
            );
        }

        $timeZone = $value instanceof DateTimeInterface ?
            $value->getTimezone()->getName() :
            ($value['timezone'] ?? '');

        /** @var array<string> $timeZoneIdentifiers */
        $timeZoneIdentifiers = DateTimeZone::listIdentifiers();

        $validator->atPath('timezone')->validate(
            $timeZone,
            [
                new Constraints\Type(['type' => 'string']),
                new Constraints\Callback(
                    [
                        'callback' => static function (string $timeZoneName, ExecutionContextInterface $context) use ($constraint, $timeZoneIdentifiers): void {
                            if (in_array($timeZoneName, $timeZoneIdentifiers, true)) {
                                return;
                            }

                            $context->buildViolation($constraint->invalidTimeZoneMessage)
                                ->setParameter('%timeZone%', $timeZoneName)
                                ->addViolation();
                        },
                    ],
                ),
            ],
        );
    }
}
