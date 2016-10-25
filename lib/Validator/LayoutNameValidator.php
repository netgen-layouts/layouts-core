<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LayoutNameValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof LayoutName) {
            throw new UnexpectedTypeException($constraint, LayoutName::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($this->layoutService->layoutNameExists(trim($value), $constraint->excludedLayoutId)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
