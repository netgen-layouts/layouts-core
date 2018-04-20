<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;

trait ParameterStructTrait
{
    /**
     * @var array
     */
    protected $parameterValues = [];

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
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param array $values
     */
    public function fill(ParameterDefinitionCollectionInterface $parameterDefinitions, array $values = [])
    {
        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $value = array_key_exists($parameterDefinition->getName(), $values) ?
                $values[$parameterDefinition->getName()] :
                $parameterDefinition->getDefaultValue();

            $this->setParameterValue($parameterDefinition->getName(), $value);

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                $this->fill($parameterDefinition, $values);
            }
        }
    }

    /**
     * Fills the struct values based on provided value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param \Netgen\BlockManager\API\Values\ParameterBasedValue $parameterBasedValue
     */
    public function fillFromValue(ParameterDefinitionCollectionInterface $parameterDefinitions, ParameterBasedValue $parameterBasedValue)
    {
        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $value = null;

            if ($parameterBasedValue->hasParameter($parameterDefinition->getName())) {
                $parameter = $parameterBasedValue->getParameter($parameterDefinition->getName());
                if ($parameter->getParameterDefinition()->getType()->getIdentifier() === $parameterDefinition->getType()->getIdentifier()) {
                    $value = $parameter->getValue();
                    $value = is_object($value) ? clone $value : $value;
                }
            }

            $this->setParameterValue($parameterDefinition->getName(), $value);

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                $this->fillFromValue($parameterDefinition, $parameterBasedValue);
            }
        }
    }

    /**
     * Fills the struct values based on provided array of values.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     * @param array $values
     * @param bool $doImport
     */
    public function fillFromHash(ParameterDefinitionCollectionInterface $parameterDefinitions, array $values = [], $doImport = false)
    {
        $importMethod = $doImport ? 'import' : 'fromHash';

        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $value = array_key_exists($parameterDefinition->getName(), $values) ?
                $parameterDefinition->getType()->{$importMethod}($parameterDefinition, $values[$parameterDefinition->getName()]) :
                $parameterDefinition->getDefaultValue();

            $this->setParameterValue($parameterDefinition->getName(), $value);

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                $this->fillFromHash($parameterDefinition, $values, $doImport);
            }
        }
    }
}
