<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator;

use Exception;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorTrait
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

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
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validate($value, $constraints, $propertyPath = null)
    {
        try {
            $violations = $this->validator->validate($value, $constraints);
        } catch (Exception $e) {
            throw ValidationException::validationFailed((string) $propertyPath, $e->getMessage(), $e);
        }

        if (count($violations) === 0) {
            return;
        }

        $propertyName = $violations[0]->getPropertyPath();
        if (empty($propertyName)) {
            $propertyName = (string) $propertyPath;
        }

        throw ValidationException::validationFailed($propertyName, $violations[0]->getMessage());
    }
}
