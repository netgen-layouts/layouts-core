<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;

interface ParameterStruct
{
    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     */
    public function setParameterValues(array $parameterValues): void;

    /**
     * Sets the parameter value to the struct.
     *
     * The value needs to be in the domain format of the value for the parameter.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function setParameterValue(string $parameterName, $parameterValue): void;

    /**
     * Returns all parameter values from the struct.
     */
    public function getParameterValues(): array;

    /**
     * Returns the parameter value with provided name or null if parameter does not exist.
     *
     * @return mixed
     */
    public function getParameterValue(string $parameterName);

    /**
     * Returns if the struct has a parameter value with provided name.
     */
    public function hasParameterValue(string $parameterName): bool;

    /**
     * Fills the struct with the default parameter values as defined in provided
     * parameter definition collection.
     */
    public function fillDefault(ParameterDefinitionCollectionInterface $definitions): void;

    /**
     * Fills the struct values based on provided parameter collection.
     */
    public function fillFromCollection(ParameterDefinitionCollectionInterface $definitions, ParameterCollectionInterface $parameters): void;

    /**
     * Fills the struct values based on provided array of values.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     */
    public function fillFromHash(ParameterDefinitionCollectionInterface $definitions, array $values = [], bool $doImport = false): void;
}
