<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ParameterTypeInterface
{
    /**
     * Returns the parameter type identifier.
     */
    public static function getIdentifier(): string;

    /**
     * Configures the options for this parameter.
     */
    public function configureOptions(OptionsResolver $optionsResolver): void;

    /**
     * Returns the parameter constraints.
     *
     * @return list<\Symfony\Component\Validator\Constraint>
     */
    public function getConstraints(ParameterDefinition $parameterDefinition, mixed $value): array;

    /**
     * Converts the parameter value from a domain format to scalar/hash format.
     *
     * @return mixed[]|string|int|float|bool|null
     */
    public function toHash(ParameterDefinition $parameterDefinition, mixed $value): array|string|int|float|bool|null;

    /**
     * Converts the provided parameter value to value usable by the domain.
     */
    public function fromHash(ParameterDefinition $parameterDefinition, mixed $value): mixed;

    /**
     * Returns the parameter value converted to a format suitable for exporting.
     *
     * This is useful if exported value is different from a stored value, for example
     * when exporting IDs from an external CMS which need to be exported not as IDs
     * but as remote IDs.
     *
     * @return mixed[]|string|int|float|bool|null
     */
    public function export(ParameterDefinition $parameterDefinition, mixed $value): array|string|int|float|bool|null;

    /**
     * Returns the parameter value converted from the exported format.
     *
     * This is useful if stored value is different from an exported value, for example
     * when importing IDs from an external CMS which need to be imported as database IDs
     * in contrast to some kind of remote ID which would be stored in the export.
     */
    public function import(ParameterDefinition $parameterDefinition, mixed $value): mixed;

    /**
     * Returns if the parameter value is empty.
     */
    public function isValueEmpty(ParameterDefinition $parameterDefinition, mixed $value): bool;
}
