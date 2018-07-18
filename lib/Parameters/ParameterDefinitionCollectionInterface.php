<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

interface ParameterDefinitionCollectionInterface
{
    /**
     * Returns all parameter definitions from the collection.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    public function getParameterDefinitions(): array;

    /**
     * Returns the parameter definition with provided name.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If the requested parameter definition does not exist
     */
    public function getParameterDefinition(string $parameterName): ParameterDefinition;

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     */
    public function hasParameterDefinition(string $parameterName): bool;
}
