<?php

namespace Netgen\BlockManager\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemLink extends ParameterDefinition
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'item_link';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('value_types'));
        $optionsResolver->setAllowedTypes('value_types', 'array');
        $optionsResolver->setDefault('value_types', array());
    }
}
