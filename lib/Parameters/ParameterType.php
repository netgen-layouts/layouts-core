<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

abstract class ParameterType implements ParameterTypeInterface
{
    public function configureOptions(OptionsResolver $optionsResolver): void {}

    /**
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     */
    public function toHash(ParameterDefinition $parameterDefinition, mixed $value): int|string|array|float|bool|null
    {
        return $value;
    }

    /**
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     */
    public function fromHash(ParameterDefinition $parameterDefinition, mixed $value): mixed
    {
        return $value;
    }

    /**
     * This is a trivial implementation that returns the value in the same format as
     * self::toHash(). Overridden implementations should take care to retain this behaviour.
     */
    public function export(ParameterDefinition $parameterDefinition, mixed $value): int|string|array|float|bool|null
    {
        return $this->toHash($parameterDefinition, $value);
    }

    /**
     * This is a trivial implementation that returns the value in the same format as
     * self::fromHash(). Overridden implementations should take care to retain this behaviour.
     */
    public function import(ParameterDefinition $parameterDefinition, mixed $value): mixed
    {
        return $this->fromHash($parameterDefinition, $value);
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, mixed $value): bool
    {
        return $value === null;
    }

    final public function getConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        if ($parameterDefinition->type::getIdentifier() !== $this::getIdentifier()) {
            throw ParameterTypeException::unsupportedParameterType(
                $parameterDefinition->type::getIdentifier(),
            );
        }

        return [
            ...$this->getRequiredConstraints($parameterDefinition, $value),
            ...$this->getValueConstraints($parameterDefinition, $value),
        ];
    }

    /**
     * Returns constraints that will be used when checking if the parameter value exists
     * or not. Usually, this method will not be overridden for most of the parameter types,
     * since for most of them, checking for a blank value is enough.
     *
     * Boolean parameter types have this overridden due to `false` value being a valid value
     * which would not validated by a `NotBlank` constraint.
     *
     * @return list<\Symfony\Component\Validator\Constraint>
     */
    protected function getRequiredConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        if ($parameterDefinition->isRequired) {
            return [
                new Constraints\NotBlank(),
            ];
        }

        return [];
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * As a rule of thumb, these constraints should assume that the provided value
     * is not null, so they need not include the NotNull constraint. This is due to
     * the fact that checking for value existence (i.e. not being null) is done
     * separately, based on if the parameter is specified as a sub-parameter of a compound
     * boolean.
     *
     * @return list<\Symfony\Component\Validator\Constraint>
     */
    abstract protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array;
}
