<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;

trait ParameterDefinitionCollectionTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    protected $parameterDefinitions = [];

    /**
     * Returns all parameter definitions from the collection.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    public function getParameterDefinitions(): array
    {
        return $this->parameterDefinitions;
    }

    /**
     * Returns the parameter definition with provided name.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If the requested parameter definition does not exist
     */
    public function getParameterDefinition(string $parameterName): ParameterDefinition
    {
        if (!$this->hasParameterDefinition($parameterName)) {
            throw ParameterException::noParameterDefinition($parameterName);
        }

        return $this->parameterDefinitions[$parameterName];
    }

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     */
    public function hasParameterDefinition(string $parameterName): bool
    {
        return array_key_exists($parameterName, $this->parameterDefinitions);
    }
}
