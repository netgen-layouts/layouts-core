<?php

namespace Netgen\BlockManager\Parameters;

interface ParameterTypeInterface
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the parameter constraints.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(ParameterDefinition $parameterDefinition, $value);

    /**
     * Returns constraints that will be used when parameter is required.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getRequiredConstraints(ParameterDefinition $parameterDefinition, $value);

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints(ParameterDefinition $parameterDefinition, $value);

    /**
     * Converts the parameter value to from a domain format to scalar/hash format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromValue($value);

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function toValue($value);

    /**
     * Returns if the parameter value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty($value);
}
