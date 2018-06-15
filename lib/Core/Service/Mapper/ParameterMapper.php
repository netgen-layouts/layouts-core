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
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param array $parameterValues
     *
     * @return array
     */
    public function mapParameters(ParameterDefinitionCollectionInterface $parameterDefinitions, array $parameterValues): array
    {
        $mappedValues = [];

        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            $parameterType = $parameterDefinition->getType();

            $value = array_key_exists($parameterName, $parameterValues) ?
                $parameterType->fromHash($parameterDefinition, $parameterValues[$parameterName]) :
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
                    $this->mapParameters($parameterDefinition, $parameterValues)
                );
            }
        }

        return $mappedValues;
    }

    /**
     * Serializes the parameter values based on provided collection of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param array $parameterValues
     * @param array $fallbackValues
     *
     * @return array
     */
    public function serializeValues(ParameterDefinitionCollectionInterface $parameterDefinitions, array $parameterValues, array $fallbackValues = []): array
    {
        $serializedValues = [];

        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            if (!array_key_exists($parameterName, $parameterValues)) {
                continue;
            }

            $serializedValues[$parameterName] = $parameterDefinition->getType()->toHash(
                $parameterDefinition,
                $parameterValues[$parameterName]
            );

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                $serializedValues = array_merge(
                    $serializedValues,
                    $this->serializeValues($parameterDefinition, $parameterValues)
                );
            }
        }

        return $serializedValues + $fallbackValues;
    }

    /**
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param array $parameterValues
     *
     * @return array
     */
    public function extractUntranslatableParameters(ParameterDefinitionCollectionInterface $parameterDefinitions, array $parameterValues): array
    {
        $untranslatableParams = [];

        foreach ($parameterDefinitions->getParameterDefinitions() as $paramName => $parameterDefinition) {
            if ($parameterDefinition->getOption('translatable') === true) {
                continue;
            }

            $untranslatableParams[$paramName] = $parameterValues[$paramName] ?? null;

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParamName => $subParameterDefinition) {
                    $untranslatableParams[$subParamName] = $parameterValues[$subParamName] ?? null;
                }
            }
        }

        return $untranslatableParams;
    }
}
