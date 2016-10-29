<?php

namespace Netgen\BlockManager\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Link extends ParameterDefinition
{
    const LINK_TYPE_URL = 'url';

    const LINK_TYPE_EMAIL = 'email';

    const LINK_TYPE_PHONE = 'phone';

    const LINK_TYPE_INTERNAL = 'internal';

    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'link';
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
