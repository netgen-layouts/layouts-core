<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;

use function array_key_exists;
use function is_object;

trait ParameterStructTrait
{
    /**
     * @var array<string, mixed>
     */
    private array $parameterValues = [];

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param array<string, mixed> $parameterValues
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
     * @param mixed $parameterValue
     */
    public function setParameterValue(string $parameterName, $parameterValue): void
    {
        $this->parameterValues[$parameterName] = $parameterValue;
    }

    /**
     * Returns all parameter values from the struct.
     *
     * @return array<string, mixed>
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
     * Fills the struct with the default parameter values as defined in provided
     * parameter definition collection.
     */
    private function fillDefault(ParameterDefinitionCollectionInterface $definitionCollection): void
    {
        foreach ($definitionCollection->getParameterDefinitions() as $name => $definition) {
            $this->setParameterValue($name, $definition->getDefaultValue());

            if ($definition instanceof CompoundParameterDefinition) {
                $this->fillDefault($definition);
            }
        }
    }

    /**
     * Fills the struct values based on provided parameter collection.
     */
    private function fillFromCollection(
        ParameterDefinitionCollectionInterface $definitionCollection,
        ParameterCollectionInterface $parameters
    ): void {
        foreach ($definitionCollection->getParameterDefinitions() as $name => $definition) {
            $value = null;

            if ($parameters->hasParameter($name)) {
                $parameter = $parameters->getParameter($name);
                if ($parameter->getParameterDefinition()->getType()::getIdentifier() === $definition->getType()::getIdentifier()) {
                    $value = $parameter->getValue();
                    $value = is_object($value) ? clone $value : $value;
                }
            }

            $this->setParameterValue($name, $value);

            if ($definition instanceof CompoundParameterDefinition) {
                $this->fillFromCollection($definition, $parameters);
            }
        }
    }

    /**
     * Fills the struct values based on provided array of values.
     *
     * If any of the parameters is missing from the input array, the default value
     * based on parameter definition from the definition collection will be used.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     *
     * @param array<string, mixed> $values
     */
    private function fillFromHash(
        ParameterDefinitionCollectionInterface $definitionCollection,
        array $values,
        bool $doImport = false
    ): void {
        foreach ($definitionCollection->getParameterDefinitions() as $name => $definition) {
            $value = $definition->getDefaultValue();
            $parameterType = $definition->getType();

            if (array_key_exists($name, $values)) {
                $value = $doImport ?
                    $parameterType->import($definition, $values[$name]) :
                    $parameterType->fromHash($definition, $values[$name]);
            }

            $this->setParameterValue($name, $value);

            if ($definition instanceof CompoundParameterDefinition) {
                $this->fillFromHash($definition, $values, $doImport);
            }
        }
    }
}
