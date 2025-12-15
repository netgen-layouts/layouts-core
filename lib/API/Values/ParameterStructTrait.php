<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use ReflectionClass;

use function array_key_exists;
use function is_object;

trait ParameterStructTrait
{
    /**
     * @var array<string, mixed>
     */
    final public array $parameterValues = [];

    final public function setParameterValue(string $parameterName, mixed $parameterValue): void
    {
        $this->parameterValues[$parameterName] = $parameterValue;
    }

    final public function getParameterValue(string $parameterName): mixed
    {
        if (!$this->hasParameterValue($parameterName)) {
            return null;
        }

        return $this->parameterValues[$parameterName];
    }

    final public function hasParameterValue(string $parameterName): bool
    {
        return array_key_exists($parameterName, $this->parameterValues);
    }

    /**
     * Fills the struct with the default parameter values as defined in provided
     * parameter definition collection.
     */
    private function fillDefault(ParameterDefinitionCollectionInterface $definitionCollection): void
    {
        foreach ($definitionCollection->parameterDefinitions as $name => $definition) {
            $this->setParameterValue($name, $definition->defaultValue);

            if ($definition->isCompound) {
                $this->fillDefault($definition);
            }
        }
    }

    /**
     * Fills the struct values based on provided parameter collection.
     */
    private function fillFromCollection(
        ParameterDefinitionCollectionInterface $definitionCollection,
        ParameterCollectionInterface $parameters,
    ): void {
        foreach ($definitionCollection->parameterDefinitions as $name => $definition) {
            $value = null;

            if ($parameters->hasParameter($name)) {
                $parameter = $parameters->getParameter($name);
                if ($parameter->parameterDefinition->type::getIdentifier() === $definition->type::getIdentifier()) {
                    $value = $parameter->value;

                    if (is_object($value) && new ReflectionClass($value::class)->isCloneable()) {
                        $value = clone $value;
                    }
                }
            }

            $this->setParameterValue($name, $value);

            if ($definition->isCompound) {
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
        bool $doImport = false,
    ): void {
        foreach ($definitionCollection->parameterDefinitions as $name => $definition) {
            $value = $definition->defaultValue;
            $parameterType = $definition->type;

            if (array_key_exists($name, $values)) {
                $value = $doImport ?
                    $parameterType->import($definition, $values[$name]) :
                    $parameterType->fromHash($definition, $values[$name]);
            }

            $this->setParameterValue($name, $value);

            if ($definition->isCompound) {
                $this->fillFromHash($definition, $values, $doImport);
            }
        }
    }
}
