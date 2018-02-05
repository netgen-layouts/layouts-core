<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

final class ParameterMapper
{
    /**
     * Maps the parameter values based on provided collection of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     *
     * @return array
     */
    public function mapParameters(ParameterCollectionInterface $parameterCollection, array $parameterValues)
    {
        $mappedValues = array();

        foreach ($parameterCollection->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            $parameterType = $parameterDefinition->getType();

            $value = array_key_exists($parameterName, $parameterValues) ?
                $parameterType->fromHash($parameterDefinition, $parameterValues[$parameterName]) :
                $parameterDefinition->getDefaultValue();

            $mappedValues[$parameterName] = new Parameter(
                array(
                    'name' => $parameterName,
                    'parameterDefinition' => $parameterDefinition,
                    'value' => $value,
                    'isEmpty' => $parameterType->isValueEmpty($parameterDefinition, $value),
                )
            );

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
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
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     * @param array $fallbackValues
     *
     * @return array
     */
    public function serializeValues(ParameterCollectionInterface $parameterCollection, array $parameterValues, array $fallbackValues = array())
    {
        $serializedValues = array();

        foreach ($parameterCollection->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            if (!array_key_exists($parameterName, $parameterValues)) {
                continue;
            }

            $serializedValues[$parameterName] = $parameterDefinition->getType()->toHash(
                $parameterDefinition,
                $parameterValues[$parameterName]
            );

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                $serializedValues = array_merge(
                    $serializedValues,
                    $this->serializeValues($parameterDefinition, $parameterValues)
                );
            }
        }

        return $serializedValues + $fallbackValues;
    }

    /**
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     *
     * @return array
     */
    public function extractUntranslatableParameters(ParameterCollectionInterface $parameterCollection, array $parameterValues)
    {
        $untranslatableParams = array();

        foreach ($parameterCollection->getParameterDefinitions() as $paramName => $parameterDefinition) {
            if ($parameterDefinition->getOption('translatable')) {
                continue;
            }

            $untranslatableParams[$paramName] = isset($parameterValues[$paramName]) ?
                $parameterValues[$paramName] :
                null;

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParamName => $subParameterDefinition) {
                    $untranslatableParams[$subParamName] = isset($parameterValues[$subParamName]) ?
                        $parameterValues[$subParamName] :
                        null;
                }
            }
        }

        return $untranslatableParams;
    }
}
