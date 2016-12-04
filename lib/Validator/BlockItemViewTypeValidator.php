<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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
