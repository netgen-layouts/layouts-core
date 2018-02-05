<?php

namespace Netgen\BlockManager\Parameters;

interface ParameterCollectionInterface
{
    /**
     * Returns the list of parameter definitions in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameterDefinitions();

    /**
     * Returns the parameter definition with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If parameter definition with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    public function getParameterDefinition($parameterName);

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameterDefinition($parameterName);
}
