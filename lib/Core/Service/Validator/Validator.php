<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
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
     *
     * @return bool
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

        return true;
    }

    /**
     * Validates the provided identifier.
     *
     * @param string $identifier
     * @param string $propertyPath
     * @param bool $isRequired
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateIdentifier($identifier, $propertyPath = null, $isRequired = false)
    {
        if (!$isRequired && $identifier === null) {
            return true;
        }

        $constraints = array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'string')),
        );

        $this->validate($identifier, $constraints, $propertyPath);

        return true;
    }

    /**
     * Validates the provided position.
     *
     * @param int $position
     * @param string $propertyPath
     * @param bool $isRequired
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validatePosition($position, $propertyPath = null, $isRequired = false)
    {
        if (!$isRequired && $position === null) {
            return true;
        }

        $constraints = array(
            new Constraints\NotBlank(),
            new Constraints\GreaterThanOrEqual(0),
            new Constraints\Type(array('type' => 'int')),
        );

        $this->validate($position, $constraints, $propertyPath);

        return true;
    }

    /**
     * Builds the "fields" array from provided parameters, used for validating set of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param array $parameterValues
     * @param bool $isRequired
     *
     * @return array
     */
    protected function buildParameterValidationFields(array $parameters, array $parameterValues, $isRequired = true)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            if ($isRequired) {
                $fields[$parameterName] = new Constraints\Required($parameter->getConstraints());
            } else {
                $fields[$parameterName] = new Constraints\Optional($parameter->getConstraints());
            }

            if ($parameter instanceof CompoundParameterInterface) {
                foreach ($parameter->getParameters() as $subParameterName => $subParameter) {
                    $parameterConstraints = $subParameter->getParameterConstraints();
                    if ($subParameter->isRequired() && isset($parameterValues[$parameterName]) && $parameterValues[$parameterName]) {
                        $parameterConstraints = array_merge($parameterConstraints, $subParameter->getBaseConstraints());
                    }

                    if ($isRequired) {
                        $fields[$subParameterName] = new Constraints\Required($parameterConstraints);
                    } else {
                        $fields[$subParameterName] = new Constraints\Optional($parameterConstraints);
                    }
                }
            }
        }

        return $fields;
    }
}
