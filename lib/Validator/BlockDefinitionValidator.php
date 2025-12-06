<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Validator\Constraint\BlockDefinition;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;

/**
 * Validates if the block definition with provided identifier exists in the system.
 */
final class BlockDefinitionValidator extends ConstraintValidator
{
    public function __construct(
        private BlockDefinitionRegistry $blockDefinitionRegistry,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof BlockDefinition) {
            throw new UnexpectedTypeException($constraint, BlockDefinition::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$constraint->allowInvalid && !$this->blockDefinitionRegistry->hasBlockDefinition($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%blockDefinition%', $value)
                ->addViolation();
        }
    }
}
