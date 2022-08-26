<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;

/**
 * Validates if passed item view type is valid for the block definition
 * and view type specified by the constraint.
 */
final class BlockItemViewTypeValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof BlockItemViewType) {
            throw new UnexpectedTypeException($constraint, BlockItemViewType::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$constraint->definition->hasViewType($constraint->viewType, $constraint->payload)) {
            $this->context->buildViolation($constraint->noViewTypeMessage)
                ->setParameter('%viewType%', $constraint->viewType)
                ->addViolation();

            return;
        }

        $viewType = $constraint->definition->getViewType($constraint->viewType, $constraint->payload);

        if (!$viewType->hasItemViewType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $constraint->viewType)
                ->setParameter('%itemViewType%', $value)
                ->addViolation();
        }
    }
}
