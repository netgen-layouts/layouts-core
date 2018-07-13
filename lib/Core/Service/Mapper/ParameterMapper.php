<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;

final class ParameterMapper
{
    /**
     * Maps the parameter values based on provided collection of parameters.
     */
    public function mapParameters(ParameterDefinitionCollectionInterface $definitions, array $values): array
    {
        $mappedValues = [];

        foreach ($definitions->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            $parameterType = $parameterDefinition->getType();

            $value = array_key_exists($parameterName, $values) ?
                $parameterType->fromHash($parameterDefinition, $values[$parameterName]) :
                $parameterDefinition->getDefaultValue();

            $mappedValues[$parameterName] = new Parameter(
                [
                    'name' => $parameterName,
                    'parameterDefinition' => $parameterDefinition,
                    'value' => $value,
                    'isEmpty' => $parameterType->isValueEmpty($parameterDefinition, $value),
                ]
            );

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                $mappedValues = array_merge(
                    $mappedValues,
                    $this->mapParameters($parameterDefinition, $values)
                );
            }
        }

        return $mappedValues;
    }

    /**
     * Serializes the parameter values based on provided collection of parameters.
     */
    public function serializeValues(ParameterDefinitionCollectionInterface $definitions, array $values, array $fallbackValues = []): array
    {
        $serializedValues = [];

        foreach ($definitions->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            if (!array_key_exists($parameterName, $values)) {
                continue;
            }

            $serializedValues[$parameterName] = $parameterDefinition->getType()->toHash(
                $parameterDefinition,
                $values[$parameterName]
            );

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                $serializedValues = array_merge(
                    $serializedValues,
                    $this->serializeValues($parameterDefinition, $values)
                );
            }
        }

        return $serializedValues + $fallbackValues;
    }

    public function extractUntranslatableParameters(ParameterDefinitionCollectionInterface $definitions, array $values): array
    {
        $untranslatableParams = [];

        foreach ($definitions->getParameterDefinitions() as $paramName => $parameterDefinition) {
            if ($parameterDefinition->getOption('translatable') === true) {
                continue;
            }

            $untranslatableParams[$paramName] = $values[$paramName] ?? null;

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParamName => $subParameterDefinition) {
                    $untranslatableParams[$subParamName] = $values[$subParamName] ?? null;
                }
            }
        }

        return $untranslatableParams;
    }
}
