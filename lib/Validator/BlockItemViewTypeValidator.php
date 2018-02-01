<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if passed item view type is valid for the block definition
 * and view type specified by the constraint.
 */
final class BlockItemViewTypeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BlockItemViewType) {
            throw new UnexpectedTypeException($constraint, BlockItemViewType::class);
        }

        if (!$constraint->definition instanceof BlockDefinitionInterface) {
            throw new UnexpectedTypeException($constraint->definition, BlockDefinitionInterface::class);
        }

        if (!is_string($constraint->viewType)) {
            throw new UnexpectedTypeException($constraint->viewType, 'string');
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$constraint->definition->hasViewType($constraint->viewType)) {
            $this->context->buildViolation($constraint->noViewTypeMessage)
                ->setParameter('%viewType%', $constraint->viewType)
                ->addViolation();

            return;
        }

        $viewType = $constraint->definition->getViewType($constraint->viewType);

        if (!$viewType->hasItemViewType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $constraint->viewType)
                ->setParameter('%itemViewType%', $value)
                ->addViolation();
        }
    }
}
