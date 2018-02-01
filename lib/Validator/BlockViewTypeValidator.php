<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if passed view type is valid for the block definition
 * specified by the constraint.
 */
final class BlockViewTypeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BlockViewType) {
            throw new UnexpectedTypeException($constraint, BlockViewType::class);
        }

        if (!$constraint->definition instanceof BlockDefinitionInterface) {
            throw new UnexpectedTypeException($constraint->definition, BlockDefinitionInterface::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$constraint->definition->hasViewType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $value)
                ->addViolation();
        }
    }
}
