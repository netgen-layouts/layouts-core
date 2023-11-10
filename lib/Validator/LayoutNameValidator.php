<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Validator\Constraint\LayoutName;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;
use function trim;

/**
 * Validates if the provided layout name already exists in the system.
 */
final class LayoutNameValidator extends ConstraintValidator
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LayoutName) {
            throw new UnexpectedTypeException($constraint, LayoutName::class);
        }

        if ($value === null || $value === '') {
            return;
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
