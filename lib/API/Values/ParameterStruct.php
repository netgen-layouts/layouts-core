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
     * Fills the struct values based on provided list of parameters and values.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param array $values
     * @param bool $useDefaults
     */
    public function fillValues(array $parameters, $values = array(), $useDefaults = true)
    {
        foreach ($parameters as $parameterName => $parameter) {
            $defaultValue = $useDefaults ? $parameter->getDefaultValue() : null;
            $value = isset($values[$parameterName]) ? $values[$parameterName] : $defaultValue;

            $this->setParameter($parameterName, $value);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->fillValues($parameter->getParameters(), $values, $useDefaults);
            }
        }
    }
}
