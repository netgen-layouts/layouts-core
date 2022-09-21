<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;

/**
 * Validates if the provided value type exists in the system.
 */
final class ValueTypeValidator extends ConstraintValidator
{
    private ValueTypeRegistry $valueTypeRegistry;

    public function __construct(ValueTypeRegistry $valueTypeRegistry)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValueType) {
            throw new UnexpectedTypeException($constraint, ValueType::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->valueTypeRegistry->hasValueType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%valueType%', $value)
                ->addViolation();
        }
    }
}
