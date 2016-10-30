<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface;
use Netgen\BlockManager\Parameters\ParameterValue;

class ParameterMapper
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface $parameterTypeRegistry
     */
    public function __construct(ParameterTypeRegistryInterface $parameterTypeRegistry)
    {
        $this->parameterTypeRegistry = $parameterTypeRegistry;
    }

    /**
     * Maps the parameter value in regard to provided list of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[] $parameterDefinitions
     * @param array $parameters
     *
     * @return array
     */
    public function mapParameters(array $parameterDefinitions, array $parameters)
    {
        $mappedValues = array();

        foreach ($parameterDefinitions as $parameterName => $parameterDefinition) {
            $rawValue = array_key_exists($parameterName, $parameters) ?
                $parameters[$parameterName] :
                null;

            $parameterType = $this->parameterTypeRegistry->getParameterType(
                $parameterDefinition->getType()
            );

            $value = $parameterType->toValue($rawValue);
            $mappedValues[$parameterName] = new ParameterValue(
                array(
                    'identifier' => $parameterName,
                    'parameterDefinition' => $parameterDefinition,
                    'parameterType' => $parameterType,
                    'value' => $value,
                    'isEmpty' => $parameterType->isValueEmpty($value),
                )
            );

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                $mappedValues = array_merge(
                    $mappedValues,
                    $this->mapParameters($parameterDefinition->getParameters(), $parameters)
                );
            }
        }

        return $mappedValues;
    }

    /**
     * Serializes the existing struct values based on provided parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[] $parameterDefinitions
     * @param array $parameterValues
     *
     * @return array
     */
    public function serializeValues(array $parameterDefinitions, array $parameterValues)
    {
        $serializedValues = array();

        foreach ($parameterDefinitions as $parameterName => $parameterDefinition) {
            if (!array_key_exists($parameterName, $parameterValues)) {
                continue;
            }

            $parameterType = $this->parameterTypeRegistry->getParameterType(
                $parameterDefinition->getType()
            );

            $serializedValues[$parameterName] = $parameterType->fromValue(
                $parameterValues[$parameterName]
            );

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                $serializedValues = array_merge(
                    $serializedValues,
                    $this->serializeValues($parameterDefinition->getParameters(), $parameterValues)
                );
            }
        }

        return $serializedValues;
    }
}
