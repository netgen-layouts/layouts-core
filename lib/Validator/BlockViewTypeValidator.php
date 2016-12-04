<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BlockViewTypeValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
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

        if (!$constraint->definition->getConfig()->hasViewType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $value)
                ->addViolation();
        }
    }
}
