<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Validator\Constraint\Layout;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;

/**
 * Validates if the published layout with provided UUID exists in the system.
 */
final class LayoutValidator extends ConstraintValidator
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Layout) {
            throw new UnexpectedTypeException($constraint, Layout::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!Uuid::isValid($value)) {
            $this->context->buildViolation($constraint->invalidFormatMessage)
                ->setParameter('%layoutId%', $value)
                ->addViolation();

            return;
        }

        if (!$constraint->allowInvalid && !$this->layoutService->layoutExists(Uuid::fromString($value))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%layoutId%', $value)
                ->addViolation();
        }
    }
}
