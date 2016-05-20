<?php

namespace Netgen\BlockManager\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

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
        /** @var \Netgen\BlockManager\Validator\Constraint\BlockViewType $constraint */
        if (!$constraint->definition->getConfig()->hasViewType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $value)
                ->addViolation();
        }
    }
}
