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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateIdentifier($identifier, $propertyPath = null, $isRequired = false)
    {
        $constraints = array(
            new Constraints\Type(array('type' => 'string')),
        );

        if ($isRequired) {
            $constraints[] = new Constraints\NotBlank();
        }

        $this->validate($identifier, $constraints, $propertyPath);
    }

    /**
     * Validates the provided position.
     *
     * @param int $position
     * @param string $propertyPath
     * @param bool $isRequired
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validatePosition($position, $propertyPath = null, $isRequired = false)
    {
        $constraints = array(
            new Constraints\GreaterThanOrEqual(0),
            new Constraints\Type(array('type' => 'int')),
        );

        if ($isRequired) {
            $constraints[] = new Constraints\NotBlank();
        }

        $this->validate($position, $constraints, $propertyPath);
    }

    /**
     * Builds the "fields" array from provided parameters and constraints, used for validating set of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\Parameter[] $parameters
     * @param array $constraints
     * @param bool $useRequired
     *
     * @return array
     */
    protected function buildParameterValidationFields(array $parameters, array $constraints, $useRequired = true)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            $paramConstraints = array();
            if (isset($constraints[$parameterName]) && is_array($constraints[$parameterName])) {
                $paramConstraints = $constraints[$parameterName];
            }

            if ($useRequired && $parameter->isRequired()) {
                $fields[$parameterName] = new Constraints\Required($paramConstraints);
            } else {
                $fields[$parameterName] = new Constraints\Optional($paramConstraints);
            }
        }

        return $fields;
    }
}
