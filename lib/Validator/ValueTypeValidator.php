<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ValueTypeValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     */
    public function __construct(ValueLoaderRegistryInterface $valueLoaderRegistry)
    {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
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

        if (!$this->valueLoaderRegistry->hasValueLoader($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%valueType%', $value)
                ->addViolation();
        }
    }
}
