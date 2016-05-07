<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class QueryTypeValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(QueryTypeRegistryInterface $queryTypeRegistry)
    {
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \Netgen\BlockManager\Validator\Constraint\QueryType $constraint */
        if (!$this->queryTypeRegistry->hasQueryType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%queryType%', $value)
                ->addViolation();
        }
    }
}
