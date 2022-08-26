<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\Validator\Constraint\BlockViewType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;

/**
 * Validates if passed view type is valid for the block definition
 * specified by the constraint.
 */
final class BlockViewTypeValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof BlockViewType) {
            throw new UnexpectedTypeException($constraint, BlockViewType::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$constraint->definition->hasViewType($value, $constraint->payload)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $value)
                ->addViolation();
        }
    }
}
