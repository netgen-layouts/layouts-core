<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;

interface ParameterStruct
{
    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param array $parameterValues
     */
    public function setParameterValues(array $parameterValues);

    /**
     * Sets the parameter value to the struct.
     *
     * The value needs to be in the domain format of the value for the parameter.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function setParameterValue($parameterName, $parameterValue);

    /**
     * Returns all parameter values from the struct.
     *
     * @return array
     */
    public function getParameterValues();

    /**
     * Returns the parameter value with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If parameter value does not exist
     *
     * @return mixed
     */
    public function getParameterValue($parameterName);

    /**
     * Returns if the struct has a parameter value with provided name.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameterValue($parameterName);

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param array $values
     */
    public function fill(ParameterDefinitionCollectionInterface $parameterDefinitions, array $values = []);

    /**
     * Fills the struct values based on provided value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param \Netgen\BlockManager\API\Values\ParameterBasedValue $parameterBasedValue
     */
    public function fillFromValue(ParameterDefinitionCollectionInterface $parameterDefinitions, ParameterBasedValue $parameterBasedValue);

    /**
     * Fills the struct values based on provided array of values.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param array $values
     */
    public function fillFromHash(ParameterDefinitionCollectionInterface $parameterDefinitions, array $values = []);
}
