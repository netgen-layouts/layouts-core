<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

interface ParameterDefinitionCollectionInterface
{
    /**
     * Returns the list of parameter definitions.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    public function getParameterDefinitions(): array;

    /**
     * Returns the parameter definition with provided name.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If parameter definition with provided name does not exist
     */
    public function getParameterDefinition(string $parameterName): ParameterDefinition;

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     */
    public function hasParameterDefinition(string $parameterName): bool;
}
