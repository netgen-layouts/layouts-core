<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
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
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param array $parameterValues
     *
     * @return array
     */
    public function mapParameters(array $parameters, array $parameterValues)
    {
        $mappedValues = array();

        foreach ($parameters as $parameterName => $parameter) {
            $rawValue = array_key_exists($parameterName, $parameterValues) ?
                $parameterValues[$parameterName] :
                null;

            $parameterType = $this->parameterTypeRegistry->getParameterType(
                $parameter->getType()
            );

            $value = $parameterType->toValue($rawValue);
            $mappedValues[$parameterName] = new ParameterValue(
                array(
                    'identifier' => $parameterName,
                    'parameter' => $parameter,
                    'parameterType' => $parameterType,
                    'value' => $value,
                    'isEmpty' => $parameterType->isValueEmpty($value),
                )
            );

            if ($parameter instanceof CompoundParameterInterface) {
                $mappedValues = array_merge(
                    $mappedValues,
                    $this->mapParameters($parameter->getParameters(), $parameterValues)
                );
            }
        }

        return $mappedValues;
    }

    /**
     * Serializes the existing struct values based on provided parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param array $parameterValues
     *
     * @return array
     */
    public function serializeValues(array $parameters, array $parameterValues)
    {
        $serializedValues = array();

        foreach ($parameters as $parameterName => $parameter) {
            if (!array_key_exists($parameterName, $parameterValues)) {
                continue;
            }

            $parameterType = $this->parameterTypeRegistry->getParameterType(
                $parameter->getType()
            );

            $serializedValues[$parameterName] = $parameterType->fromValue(
                $parameterValues[$parameterName]
            );

            if ($parameter instanceof CompoundParameterInterface) {
                $serializedValues = array_merge(
                    $serializedValues,
                    $this->serializeValues($parameter->getParameters(), $parameterValues)
                );
            }
        }

        return $serializedValues;
    }
}
