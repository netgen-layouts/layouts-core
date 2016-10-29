<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;

abstract class CompoundParameterHandler extends ParameterHandler
{
    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     *
     * @return array
     */
    public function convertOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return array(
            'parameters' => $parameterDefinition->getParameters(),
        );
    }

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterDefinitionInterface $parameterDefinition, $parameterName, array $options)
    {
        return array(
            'label' => false,
            'label_prefix' => $options['label_prefix'],
            'property_path_prefix' => $options['property_path_prefix'],
        ) + parent::getDefaultOptions($parameterDefinition, $parameterName, $options);
    }
}
