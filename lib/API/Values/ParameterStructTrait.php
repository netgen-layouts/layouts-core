<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

trait ParameterStructTrait
{
    /**
     * @var array
     */
    protected $parameterValues = array();

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param array $parameterValues
     */
    public function setParameterValues(array $parameterValues)
    {
        $this->parameterValues = $parameterValues;
    }

    /**
     * Sets the parameter value to the struct.
     *
     * The value needs to be in the domain format of the value for the parameter.
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
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If parameter value does not exist
     *
     * @return mixed
     */
    public function getParameterValue($parameterName)
    {
        if (!$this->hasParameterValue($parameterName)) {
            throw ParameterException::noParameterValue($parameterName);
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
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $values
     */
    public function fill(ParameterCollectionInterface $parameterCollection, array $values = array())
    {
        foreach ($parameterCollection->getParameters() as $parameter) {
            $value = array_key_exists($parameter->getName(), $values) ?
                $values[$parameter->getName()] :
                $parameter->getDefaultValue();

            $this->setParameterValue($parameter->getName(), $value);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->fill($parameter, $values);
            }
        }
    }

    /**
     * Fills the struct values based on provided value object.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param \Netgen\BlockManager\API\Values\ParameterAwareValue $parameterAwareValue
     */
    public function fillFromValue(ParameterCollectionInterface $parameterCollection, ParameterAwareValue $parameterAwareValue)
    {
        foreach ($parameterCollection->getParameters() as $parameter) {
            $value = null;

            if ($parameterAwareValue->hasParameter($parameter->getName())) {
                $valueParameter = $parameterAwareValue->getParameter($parameter->getName());
                if ($valueParameter->getParameter()->getType()->getIdentifier() === $parameter->getType()->getIdentifier()) {
                    $value = $valueParameter->getValue();
                    $value = is_object($value) ? clone $value : $value;
                }
            }

            $this->setParameterValue($parameter->getName(), $value);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->fillFromValue($parameter, $parameterAwareValue);
            }
        }
    }

    /**
     * Fills the struct values based on provided array of values.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $values
     */
    public function fillFromHash(ParameterCollectionInterface $parameterCollection, array $values = array())
    {
        foreach ($parameterCollection->getParameters() as $parameter) {
            $value = array_key_exists($parameter->getName(), $values) ?
                $parameter->getType()->fromHash($parameter, $values[$parameter->getName()]) :
                $parameter->getDefaultValue();

            $this->setParameterValue($parameter->getName(), $value);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->fillFromHash($parameter, $values);
            }
        }
    }
}
