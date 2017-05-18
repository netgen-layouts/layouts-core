<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValueTypeValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    protected $valueTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface $valueTypeRegistry
     */
    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValueType) {
            throw new UnexpectedTypeException($constraint, ValueType::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $enabledValueTypes = array_keys($this->valueTypeRegistry->getValueTypes(true));

        if (!in_array($value, $enabledValueTypes, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%valueType%', $value)
                ->addViolation();
        }
    }
}
