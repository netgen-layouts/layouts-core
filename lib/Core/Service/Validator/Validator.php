<?php

namespace Netgen\BlockManager\Core\Service\Validator;

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
    public function validateId($id, $propertyPath = null)
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
     * @param string $identifier
     * @param string $propertyPath
     * @param bool $isRequired
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateIdentifier($identifier, $propertyPath = null, $isRequired = false)
    {
        if (!$isRequired && $identifier === null) {
            return;
        }

        $constraints = [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'string']),
            new Constraints\Regex(
                [
                    'pattern' => '/^[A-Za-z]([A-Za-z0-9_])*$/',
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
     * @param int $position
     * @param string $propertyPath
     * @param bool $isRequired
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validatePosition($position, $propertyPath = null, $isRequired = false)
    {
        if (!$isRequired && $position === null) {
            return;
        }

        $constraints = [
            new Constraints\NotBlank(),
            new Constraints\GreaterThanOrEqual(0),
            new Constraints\Type(['type' => 'int']),
        ];

        $this->validate($position, $constraints, $propertyPath);
    }

    /**
     * Validates the provided offset and limit values to be integers.
     *
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateOffsetAndLimit($offset, $limit)
    {
        $this->validate(
            $offset,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'int']),
            ],
            'offset'
        );

        if ($limit !== null) {
            $this->validate(
                $limit,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'int']),
                ],
                'limit'
            );
        }
    }

    /**
     * Validates the provided locale.
     *
     * @param string $locale
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLocale($locale, $propertyPath = null)
    {
        $this->validate(
            $locale,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new Constraints\Locale(),
            ],
            $propertyPath
        );
    }
}
