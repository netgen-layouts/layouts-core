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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
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
     * Builds the "fields" array from provided parameters, used for validating set of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param bool $useRequired
     *
     * @return array
     */
    protected function buildParameterValidationFields(array $parameters, $useRequired = true)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            if ($useRequired && $parameter->isRequired()) {
                $fields[$parameterName] = new Constraints\Required($parameter->getConstraints());
            } else {
                $fields[$parameterName] = new Constraints\Optional($parameter->getConstraints());
            }
        }

        return $fields;
    }
}
