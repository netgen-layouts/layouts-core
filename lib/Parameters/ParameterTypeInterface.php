<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ParameterTypeInterface
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Configures the options for this parameter.
     */
    public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Returns the parameter constraints.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(ParameterDefinition $parameterDefinition, $value);

    /**
     * Converts the parameter value from a domain format to scalar/hash format.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function toHash(ParameterDefinition $parameterDefinition, $value);

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromHash(ParameterDefinition $parameterDefinition, $value);

    /**
     * Returns the parameter value converted to a format suitable for exporting.
     *
     * This is useful if exported value is different from a stored value, for example
     * when exporting IDs from an external CMS which need to be exported not as IDs
     * but as remote IDs.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function export(ParameterDefinition $parameterDefinition, $value);

    /**
     * Returns the parameter value converted from the exported format.
     *
     * This is useful if stored value is different from an exported value, for example
     * when importing IDs from an external CMS which need to be imported as database IDs
     * in contrast to some kind of remote ID which would be stored in the export.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function import(ParameterDefinition $parameterDefinition, $value);

    /**
     * Returns if the parameter value is empty.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value);
}
