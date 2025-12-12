<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;

use function array_key_exists;

trait ParameterDefinitionCollectionTrait
{
    final public protected(set) array $parameterDefinitions = [];

    final public function getParameterDefinition(string $parameterName): ParameterDefinition
    {
        if (!$this->hasParameterDefinition($parameterName)) {
            throw ParameterException::noParameterDefinition($parameterName);
        }

        return $this->parameterDefinitions[$parameterName];
    }

    final public function hasParameterDefinition(string $parameterName): bool
    {
        return array_key_exists($parameterName, $this->parameterDefinitions);
    }
}
