<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterValue;

class ParameterMapper
{
    /**
     * Maps the parameter value in regard to provided list of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     *
     * @return array
     */
    public function mapParameters(ParameterCollectionInterface $parameterCollection, array $parameterValues)
    {
        $mappedValues = array();

        foreach ($parameterCollection->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $parameterType = $parameter->getType();

            $value = array_key_exists($parameterName, $parameterValues) ?
                $parameterType->fromHash($parameterValues[$parameterName]) :
                $parameter->getDefaultValue();

            $mappedValues[$parameterName] = new ParameterValue(
                array(
                    'name' => $parameterName,
                    'parameter' => $parameter,
                    'parameterType' => $parameterType,
                    'value' => $value,
                    'isEmpty' => $parameterType->isValueEmpty($value),
                )
            );

            if ($parameter instanceof CompoundParameterInterface) {
                $mappedValues = array_merge(
                    $mappedValues,
                    $this->mapParameters($parameter, $parameterValues)
                );
            }
        }

        return $mappedValues;
    }

    /**
     * Serializes the existing struct values based on provided parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     *
     * @return array
     */
    public function serializeValues(ParameterCollectionInterface $parameterCollection, array $parameterValues)
    {
        $serializedValues = array();

        foreach ($parameterCollection->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            if (!array_key_exists($parameterName, $parameterValues)) {
                continue;
            }

            $serializedValues[$parameterName] = $parameter->getType()->toHash(
                $parameterValues[$parameterName]
            );

            if ($parameter instanceof CompoundParameterInterface) {
                $serializedValues = array_merge(
                    $serializedValues,
                    $this->serializeValues($parameter, $parameterValues)
                );
            }
        }

        return $serializedValues;
    }
}
