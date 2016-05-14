<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Configuration\LayoutType\Registry;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class LayoutValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\Registry
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\LayoutType\Registry $layoutTypeRegistry
     */
    public function __construct(Registry $layoutTypeRegistry)
    {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \Netgen\BlockManager\Validator\Constraint\Layout $constraint */

        if (!$this->layoutTypeRegistry->hasLayoutType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%type%', $value)
                ->addViolation();
        }
    }
}
