<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\ValueObject;

abstract class ParameterStruct extends ValueObject
{
    /**
     * @var array
     */
    protected $parameterValues = array();

    /**
     * Sets the parameter value to the struct.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function setParameterValue($parameterName, $parameterValue)
    {
        $this->parameterValues[$parameterName] = $parameterValue;
    }

    /**
     * Returns all parameter values from the struct.
     *
     * @return array
     */
    public function getParameterValues()
    {
        return $this->parameterValues;
    }

    /**
     * Returns the parameter value with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter value does not exist
     *
     * @return mixed
     */
    public function getParameterValue($parameterName)
    {
        if (!$this->hasParameterValue($parameterName)) {
            throw new InvalidArgumentException(
                'parameterName',
                sprintf(
                    'Parameter value with name "%s" does not exist in the struct.',
                    $parameterName
                )
            );
        }

        return $this->parameterValues[$parameterName];
    }

    /**
     * Returns if the struct has a parameter value with provided name.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameterValue($parameterName)
    {
        return array_key_exists($parameterName, $this->parameterValues);
    }

    /**
     * Fills the struct values based on provided list of parameters and values.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $values
     * @param bool $useDefaults
     */
    public function fillValues(ParameterCollectionInterface $parameterCollection, $values = array(), $useDefaults = true)
    {
        foreach ($parameterCollection->getParameters() as $parameter) {
            $value = $this->buildValue($parameter, $values, $useDefaults);
            $this->setParameterValue($parameter->getName(), $value);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->fillValues($parameter, $values, $useDefaults);
            }
        }
    }

    /**
     * Builds the value suitable for usage by the struct for provided parameter.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param array $values
     * @param bool $useDefaults
     *
     * @return mixed
     */
    protected function buildValue(ParameterInterface $parameter, array $values = array(), $useDefaults = true)
    {
        if (!array_key_exists($parameter->getName(), $values)) {
            return $useDefaults ? $parameter->getDefaultValue() : null;
        }

        $value = $values[$parameter->getName()];
        if ($value instanceof ParameterValue) {
            $value = $value->getValue();

            return is_object($value) ? clone $value : $value;
        }

        return $parameter->getType()->toValue($value);
    }
}
