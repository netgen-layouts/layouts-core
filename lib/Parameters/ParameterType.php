<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

abstract class ParameterType implements ParameterTypeInterface
{
    public function configureOptions(OptionsResolver $optionsResolver): void {}

    public function getConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        if ($parameterDefinition->getType()::getIdentifier() !== $this::getIdentifier()) {
            throw ParameterTypeException::unsupportedParameterType(
                $parameterDefinition->getType()::getIdentifier(),
            );
        }

        return [
            ...$this->getRequiredConstraints($parameterDefinition, $value),
            ...$this->getValueConstraints($parameterDefinition, $value),
        ];
    }

    /**
     * Converts the parameter value from a domain format to scalar/hash format.
     *
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function toHash(ParameterDefinition $parameterDefinition, $value)
    {
        return $value;
    }

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromHash(ParameterDefinition $parameterDefinition, $value)
    {
        return $value;
    }

    /**
     * Returns the parameter value converted to a format suitable for exporting.
     *
     * This is useful if exported value is different from a stored value, for example
     * when exporting IDs from an external CMS which need to be exported not as IDs
     * but as remote IDs.
     *
     * This is a trivial implementation that returns the value in the same format as
     * self::toHash(). Overridden implementations should take care to retain this behaviour.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function export(ParameterDefinition $parameterDefinition, $value)
    {
        return $this->toHash($parameterDefinition, $value);
    }

    /**
     * Returns the parameter value converted from the exported format.
     *
     * This is useful if stored value is different from an exported value, for example
     * when importing IDs from an external CMS which need to be imported as database IDs
     * in contrast to some kind of remote ID which would be stored in the export.
     *
     * This is a trivial implementation that returns the value in the same format as
     * self::fromHash(). Overridden implementations should take care to retain this behaviour.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function import(ParameterDefinition $parameterDefinition, $value)
    {
        return $this->fromHash($parameterDefinition, $value);
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null;
    }

    /**
     * Returns constraints that will be used when checking if the parameter value exists
     * or not. Usually, this method will not be overridden for most of the parameter types,
     * since for most of them, checking for a blank value is enough.
     *
     * Boolean parameter types have this overridden due to `false` value being a valid value
     * which would not validated by a `NotBlank` constraint.
     *
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getRequiredConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        if ($parameterDefinition->isRequired()) {
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
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    abstract protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array;
}
