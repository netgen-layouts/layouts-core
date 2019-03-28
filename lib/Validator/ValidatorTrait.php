<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Exception\Validation\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

trait ValidatorTrait
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    public function setValidator(ValidatorInterface $validator): void
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
    protected function validate($value, $constraints, ?string $propertyPath = null): void
    {
        try {
            $violations = $this->validator->validate($value, $constraints);
        } catch (Throwable $t) {
            throw ValidationException::validationFailed($propertyPath ?? '', $t->getMessage(), $t);
        }

        if (count($violations) === 0) {
            return;
        }

        $propertyName = $violations[0]->getPropertyPath() ?? '';
        if ($propertyName === '') {
            $propertyName = $propertyPath ?? '';
        }

        throw ValidationException::validationFailed($propertyName, $violations[0]->getMessage());
    }
}
