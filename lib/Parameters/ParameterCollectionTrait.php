<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;

trait ParameterCollectionTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    protected $parameterDefinitions = [];

    /**
     * Returns the list of parameter definitions in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameterDefinitions()
    {
        return $this->parameterDefinitions;
    }

    /**
     * Returns the parameter definition with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    public function getParameterDefinition($parameterName)
    {
        if ($this->hasParameterDefinition($parameterName)) {
            return $this->parameterDefinitions[$parameterName];
        }

        throw ParameterException::noParameterDefinition($parameterName);
    }

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameterDefinition($parameterName)
    {
        return array_key_exists($parameterName, $this->parameterDefinitions);
    }
}
