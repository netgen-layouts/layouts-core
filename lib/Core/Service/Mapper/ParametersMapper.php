<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;

trait ParametersMapper
{
    /**
     * Maps the parameter values in regard to provided list of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param array $parameterValues
     *
     * @return array
     */
    protected function mapParameters(array $parameters, array $parameterValues)
    {
        $mappedValues = array();

        foreach ($parameters as $parameterName => $parameter) {
            $mappedValues[$parameterName] = isset($parameterValues[$parameterName]) ?
                $parameterValues[$parameterName] :
                null;

            if ($parameter instanceof CompoundParameterInterface) {
                $mappedValues = array_merge(
                    $mappedValues,
                    $this->mapParameters($parameter->getParameters(), $parameterValues)
                );
            }
        }

        return $mappedValues;
    }
}
