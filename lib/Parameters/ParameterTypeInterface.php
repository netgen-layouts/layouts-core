<?php

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
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Returns the parameter constraints.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(ParameterDefinitionInterface $parameterDefinition, $value);

    /**
     * Converts the parameter value from a domain format to scalar/hash format.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function toHash(ParameterDefinitionInterface $parameterDefinition, $value);

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value);

    /**
     * Returns the parameter value converted to a format suitable for exporting.
     *
     * This is useful if exported value is different from a stored value, for example
     * when exporting IDs from an external CMS which need to be exported not as IDs
     * but as remote IDs.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function export(ParameterDefinitionInterface $parameterDefinition, $value);

    /**
     * Returns the parameter value converted from the exported format.
     *
     * This is useful if stored value is different from an exported value, for example
     * when importing IDs from an external CMS which need to be imported as database IDs
     * in contrast to some kind of remote ID which would be stored in the export.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param mixed $value
     *
     * @return mixed
     */
    public function import(ParameterDefinitionInterface $parameterDefinition, $value);

    /**
     * Returns if the parameter value is empty.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value);
}
