<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ValueTypeValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    protected $valueTypes;

    /**
     * Constructor.
     *
     * @param array $valueTypes
     */
    public function __construct(array $valueTypes = array())
    {
        $this->valueTypes = $valueTypes;
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

        if (!in_array($value, $this->valueTypes)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%valueType%', $value)
                ->addViolation();
        }
    }
}
