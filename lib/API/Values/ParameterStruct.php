<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\ValueObject;

abstract class ParameterStruct extends ValueObject implements ParameterCollectionInterface
{
    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Sets the parameters to the struct.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets the parameter to the struct.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function setParameter($parameterName, $parameterValue)
    {
        $this->parameters[$parameterName] = $parameterValue;
    }

    /**
     * Returns all parameters from the struct.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter with provided identifier.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter does not exist
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw new InvalidArgumentException(
                'parameterName',
                'Parameter does not exist in the struct.'
            );
        }

        return $this->parameters[$parameterName];
    }

    /**
     * Returns if the struct has a parameter with provided identifier.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return isset($this->parameters[$parameterName]);
    }

    /**
     * Returns the default parameter values based on provided list of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     *
     * @return array
     */
    public function getDefaultValues(array $parameters)
    {
        $defaultValues = array();

        foreach ($parameters as $parameterName => $parameter) {
            $defaultValues[$parameterName] = $parameter->getDefaultValue();

            if ($parameter instanceof CompoundParameterInterface) {
                foreach ($parameter->getParameters() as $subParameterName => $subParameter) {
                    $defaultValues[$subParameterName] = $parameter->getDefaultValue();
                }
            }
        }

        return $defaultValues;
    }
}
