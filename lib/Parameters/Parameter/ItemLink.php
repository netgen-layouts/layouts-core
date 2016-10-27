<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Validator\Constraint\ItemLink as ItemLinkConstraint;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemLink extends Parameter
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

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints($value)
    {
        return array(
            new ItemLinkConstraint(),
        );
    }
}
