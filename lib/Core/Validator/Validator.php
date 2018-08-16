<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Validator;

use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints;

abstract class Validator
{
    use ValidatorTrait;

    /**
     * Validates the provided ID to be an integer or string.
     *
     * Use the $propertyPath to change the name of the validated property in the error message.
     *
     * @param int|string $id
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateId($id, ?string $propertyPath = null): void
    {
        $this->validate(
            $id,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'scalar']),
            ],
            $propertyPath
        );
    }

    /**
     * Validates the provided identifier to be a string.
     *
     * If $isRequired is set to false, null value is also allowed.
     *
     * Use the $propertyPath to change the name of the validated property in the error message.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateIdentifier(string $identifier, ?string $propertyPath = null): void
    {
        $constraints = [
            new Constraints\NotBlank(),
            new Constraints\Regex(
                [
                    'pattern' => '/^[A-Za-z0-9_]*[A-Za-z][A-Za-z0-9_]*$/',
                ]
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
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
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
     * Validates the provided offset and limit values to be integers.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateOffsetAndLimit(?int $offset, ?int $limit): void
    {
        $this->validate(
            $offset,
            [
                new Constraints\NotBlank(),
            ],
            'offset'
        );

        if ($limit !== null) {
            $this->validate(
                $limit,
                [
                    new Constraints\NotBlank(),
                ],
                'limit'
            );
        }
    }

    /**
     * Validates the provided locale.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLocale(string $locale, ?string $propertyPath = null): void
    {
        $this->validate(
            $locale,
            [
                new Constraints\NotBlank(),
                new LocaleConstraint(),
            ],
            $propertyPath
        );
    }
}
