<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Validator\Constraint\Locale as LocaleConstraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

use function sprintf;

trait ValidatorTrait
{
    private ValidatorInterface $validator;

    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * Validates the provided identifier to be a string.
     *
     * Use the $propertyPath to change the name of the validated property in the error message.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateIdentifier(string $identifier, ?string $propertyPath = null): void
    {
        $constraints = [
            new Constraints\NotBlank(),
            new Constraints\Regex(
                [
                    'pattern' => '/^[A-Za-z0-9_]*[A-Za-z][A-Za-z0-9_]*$/',
                ],
            ),
        ];

        $this->validate($identifier, $constraints, $propertyPath);
    }

    /**
     * Validates the provided position to be an integer greater than or equal to 0.
     *
     * If $isRequired is set to false, null value is also allowed.
     *
     * Use the $propertyPath to change the name of the validated property in the error message.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validatePosition(?int $position, ?string $propertyPath = null, bool $isRequired = false): void
    {
        if (!$isRequired && $position === null) {
            return;
        }

        $constraints = [
            new Constraints\NotBlank(),
            new Constraints\GreaterThanOrEqual(0),
        ];

        $this->validate($position, $constraints, $propertyPath);
    }

    /**
     * Validates the provided locale.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLocale(string $locale, ?string $propertyPath = null): void
    {
        $this->validate(
            $locale,
            [
                new Constraints\NotBlank(),
                new LocaleConstraint(),
            ],
            $propertyPath,
        );
    }

    /**
     * Validates the value against a set of provided constraints.
     *
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint|\Symfony\Component\Validator\Constraint[] $constraints
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    private function validate($value, $constraints, ?string $propertyPath = null): void
    {
        try {
            $violations = $this->validator->validate($value, $constraints);
        } catch (Throwable $t) {
            throw ValidationException::validationFailed($propertyPath ?? '', $t->getMessage(), $t);
        }

        if (!$violations->has(0)) {
            return;
        }

        $propertyPath = sprintf('%s%s', $propertyPath ?? '', $violations->get(0)->getPropertyPath());

        throw ValidationException::validationFailed($propertyPath, (string) $violations->get(0)->getMessage());
    }
}
