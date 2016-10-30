<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;

trait ValidatorTrait
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates the value against a set of provided constraints.
     *
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint|\Symfony\Component\Validator\Constraint[] $constraints
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validate($value, $constraints, $propertyPath = null)
    {
        try {
            $violations = $this->validator->validate($value, $constraints);
        } catch (Exception $e) {
            throw new ValidationFailedException($e->getMessage(), 0, $e);
        }

        if (count($violations) > 0) {
            throw new ValidationFailedException(
                sprintf(
                    'There was an error validating "%s": %s',
                    $propertyPath !== null ? $propertyPath : $violations[0]->getPropertyPath(),
                    $violations[0]->getMessage()
                )
            );
        }
    }
}
