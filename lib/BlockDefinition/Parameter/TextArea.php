<?php

namespace Netgen\BlockManager\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextArea extends Parameter
{
    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
    }

    /**
     * Returns the Symfony form type which matches this parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return 'textarea';
    }

    /**
     * Maps the parameter attributes to Symfony form options.
     *
     * @return array
     */
    public function mapFormTypeOptions()
    {
        return array();
    }
}
