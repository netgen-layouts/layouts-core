<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\Validator\Constraint\RuleGroup;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;

/**
 * Validates if the published rule group with provided UUID exists in the system.
 */
final class RuleGroupValidator extends ConstraintValidator
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof RuleGroup) {
            throw new UnexpectedTypeException($constraint, RuleGroup::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!Uuid::isValid($value)) {
            $this->context->buildViolation($constraint->invalidFormatMessage)
                ->setParameter('%ruleGroupId%', $value)
                ->addViolation();

            return;
        }

        if (!$constraint->allowInvalid && !$this->layoutResolverService->ruleGroupExists(Uuid::fromString($value))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%ruleGroupId%', $value)
                ->addViolation();
        }
    }
}
