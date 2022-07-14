<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Generator;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;

use function array_key_exists;

final class ParameterMapper
{
    /**
     * Maps the parameter values based on provided collection of parameters.
     *
     * @param array<string, mixed> $values
     *
     * @return \Generator<string, \Netgen\Layouts\Parameters\Parameter>
     */
    public function mapParameters(ParameterDefinitionCollectionInterface $definitions, array $values): Generator
    {
        foreach ($definitions->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            $parameterType = $parameterDefinition->getType();

            $value = array_key_exists($parameterName, $values) ?
                $parameterType->fromHash($parameterDefinition, $values[$parameterName]) :
                $parameterDefinition->getDefaultValue();

            yield $parameterName => Parameter::fromArray(
                [
                    'name' => $parameterName,
                    'parameterDefinition' => $parameterDefinition,
                    'value' => $value,
                    'isEmpty' => $parameterType->isValueEmpty($parameterDefinition, $value),
                ],
            );

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                yield from $this->mapParameters($parameterDefinition, $values);
            }
        }
    }

    /**
     * Serializes the parameter values based on provided collection of parameters.
     *
     * @param array<string, mixed> $values
     * @param array<string, mixed> $fallbackValues
     *
     * @return \Generator<string, mixed>
     */
    public function serializeValues(ParameterDefinitionCollectionInterface $definitions, array $values, array $fallbackValues = []): Generator
    {
        yield from $fallbackValues;

        foreach ($definitions->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            if (!array_key_exists($parameterName, $values)) {
                continue;
            }

            yield $parameterName => $parameterDefinition->getType()->toHash(
                $parameterDefinition,
                $values[$parameterName],
            );

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                yield from $this->serializeValues($parameterDefinition, $values);
            }
        }
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return \Generator<string, mixed>
     */
    public function extractUntranslatableParameters(ParameterDefinitionCollectionInterface $definitions, array $values): Generator
    {
        foreach ($definitions->getParameterDefinitions() as $paramName => $parameterDefinition) {
            if ($parameterDefinition->getOption('translatable') === true) {
                continue;
            }

            yield $paramName => $values[$paramName] ?? null;

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParamName => $subParameterDefinition) {
                    yield $subParamName => $values[$subParamName] ?? null;
                }
            }
        }
    }
}
