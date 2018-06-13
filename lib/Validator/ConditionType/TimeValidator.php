<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\ConditionType;

use DateTimeInterface;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Netgen\BlockManager\Validator\Constraint\ConditionType\Time;
use Netgen\BlockManager\Validator\Constraint\DateTime as DateTimeConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is a valid value for "time" condition type.
 */
final class TimeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Time) {
            throw new UnexpectedTypeException($constraint, Time::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->validate(
            $value,
            [
                new Constraints\Collection(
                    [
                        'fields' => [
                            'from' => new Constraints\Required(
                                [
                                    new DateTimeConstraint(['allowArray' => true]),
                                ]
                            ),
                            'to' => new Constraints\Required(
                                [
                                    new DateTimeConstraint(['allowArray' => true]),
                                ]
                            ),
                        ],
                    ]
                ),
            ]
        );

        $visibleFrom = isset($value['from']) && is_array($value['from']) ?
            DateTimeUtils::createFromArray($value['from']) :
            null;

        $visibleTo = isset($value['to']) && is_array($value['to']) ?
            DateTimeUtils::createFromArray($value['to']) :
            null;

        if (!$visibleFrom instanceof DateTimeInterface || !$visibleTo instanceof DateTimeInterface) {
            return;
        }

        if ($visibleTo > $visibleFrom) {
            return;
        }

        $this->context
            ->buildViolation($constraint->toLowerThanFromMessage)
            ->atPath('to')
            ->addViolation();
    }
}
