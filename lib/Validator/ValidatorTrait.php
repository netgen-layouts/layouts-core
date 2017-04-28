<?php

namespace Netgen\BlockManager\Validator;

use Exception;
use Netgen\BlockManager\Exception\Validation\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationFailedException If the validation failed
     */
    public function validate($value, $constraints, $propertyPath = null)
    {
        try {
            $violations = $this->validator->validate($value, $constraints);
        } catch (Exception $e) {
            throw new ValidationFailedException((string) $propertyPath, $e->getMessage(), $e);
        }

        if (count($violations) === 0) {
            return;
        }

        $propertyName = $violations[0]->getPropertyPath();
        if (empty($propertyName)) {
            $propertyName = (string) $propertyPath;
        }

        throw new ValidationFailedException($propertyName, $violations[0]->getMessage());
    }
}
