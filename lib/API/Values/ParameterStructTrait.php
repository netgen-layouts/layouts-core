<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;

trait ParameterStructTrait
{
    /**
     * @var array
     */
    private $parameterValues = [];

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     */
    public function setParameterValues(array $parameterValues): void
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
    public function setParameterValue(string $parameterName, $parameterValue): void
    {
        $this->parameterValues[$parameterName] = $parameterValue;
    }

    /**
     * Returns all parameter values from the struct.
     */
    public function getParameterValues(): array
    {
        return $this->parameterValues;
    }

    /**
     * Returns the parameter value with provided name or null if parameter does not exist.
     *
     * @return mixed
     */
    public function getParameterValue(string $parameterName)
    {
        if (!$this->hasParameterValue($parameterName)) {
            return null;
        }

        return $this->parameterValues[$parameterName];
    }

    /**
     * Returns if the struct has a parameter value with provided name.
     */
    public function hasParameterValue(string $parameterName): bool
    {
        return array_key_exists($parameterName, $this->parameterValues);
    }

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     */
    public function fill(ParameterDefinitionCollectionInterface $parameterDefinitions, array $values = []): void
    {
        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $value = array_key_exists($parameterDefinition->getName(), $values) ?
                $values[$parameterDefinition->getName()] :
                $parameterDefinition->getDefaultValue();

            $this->setParameterValue($parameterDefinition->getName(), $value);

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                $this->fill($parameterDefinition, $values);
            }
        }
    }

    /**
     * Fills the struct values based on provided value.
     */
    public function fillFromValue(ParameterDefinitionCollectionInterface $parameterDefinitions, ParameterBasedValue $parameterBasedValue): void
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

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
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
     */
    public function fillFromHash(ParameterDefinitionCollectionInterface $parameterDefinitions, array $values = [], bool $doImport = false): void
    {
        $importMethod = $doImport ? 'import' : 'fromHash';

        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $value = array_key_exists($parameterDefinition->getName(), $values) ?
                $parameterDefinition->getType()->{$importMethod}($parameterDefinition, $values[$parameterDefinition->getName()]) :
                $parameterDefinition->getDefaultValue();

            $this->setParameterValue($parameterDefinition->getName(), $value);

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                $this->fillFromHash($parameterDefinition, $values, $doImport);
            }
        }
    }
}
