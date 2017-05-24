<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface ParameterStruct
{
    /**
     * Sets the parameter values to the struct.
     *
     * @param array $parameterValues
     */
    public function setParameterValues(array $parameterValues);

    /**
     * Sets the parameter value to the struct.
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
     * Fills the struct values based on provided list of parameters and values.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $values
     * @param bool $useDefaults
     */
    public function fillValues(ParameterCollectionInterface $parameterCollection, $values = array(), $useDefaults = true);
}
