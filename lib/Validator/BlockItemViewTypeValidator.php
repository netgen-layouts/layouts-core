<?php

namespace Netgen\BlockManager\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class BlockItemViewTypeValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \Netgen\BlockManager\Validator\Constraint\BlockItemViewType $constraint */
        if (!$constraint->definition->getConfig()->hasViewType($constraint->viewType)) {
            $this->context->buildViolation($constraint->noViewTypeMessage)
                ->setParameter('%viewType%', $constraint->viewType)
                ->addViolation();

            return;
        }

        $viewType = $constraint->definition->getConfig()->getViewType($constraint->viewType);

        if (!$viewType->hasItemViewType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $constraint->viewType)
                ->setParameter('%itemViewType%', $value)
                ->addViolation();
        }
    }
}
