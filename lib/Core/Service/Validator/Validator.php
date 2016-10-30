<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints;

abstract class Validator
{
    use ValidatorTrait;

    /**
     * Validates the provided ID.
     *
     * @param int|string $id
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateId($id, $propertyPath = null)
    {
        $this->validate(
            $id,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'scalar')),
            ),
            $propertyPath
        );
    }

    /**
     * Validates the provided identifier.
     *
     * @param string $identifier
     * @param string $propertyPath
     * @param bool $isRequired
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateIdentifier($identifier, $propertyPath = null, $isRequired = false)
    {
        if (!$isRequired && $identifier === null) {
            return;
        }

        $constraints = array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'string')),
        );

        $this->validate($identifier, $constraints, $propertyPath);
    }

    /**
     * Validates the provided position.
     *
     * @param int $position
     * @param string $propertyPath
     * @param bool $isRequired
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validatePosition($position, $propertyPath = null, $isRequired = false)
    {
        if (!$isRequired && $position === null) {
            return;
        }

        $constraints = array(
            new Constraints\NotBlank(),
            new Constraints\GreaterThanOrEqual(0),
            new Constraints\Type(array('type' => 'int')),
        );

        $this->validate($position, $constraints, $propertyPath);
    }

    /**
     * Validates the provided offset and limit values.
     *
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateOffsetAndLimit($offset, $limit)
    {
        $this->validate(
            $offset,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'int')),
            ),
            'offset'
        );

        if ($limit !== null) {
            $this->validate(
                $limit,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'int')),
                ),
                'limit'
            );
        }
    }
}
