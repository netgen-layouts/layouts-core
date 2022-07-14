<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;

use function array_key_exists;

trait ParameterDefinitionCollectionTrait
{
    /**
     * @var \Netgen\Layouts\Parameters\ParameterDefinition[]
     */
    protected array $parameterDefinitions = [];

    /**
     * Returns all parameter definitions from the collection.
     *
     * @return \Netgen\Layouts\Parameters\ParameterDefinition[]
     */
    public function getParameterDefinitions(): array
    {
        return $this->parameterDefinitions;
    }

    /**
     * Returns the parameter definition with provided name.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterException If the requested parameter definition does not exist
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
