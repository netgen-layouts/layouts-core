<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Stubs;

use Netgen\BlockManager\BlockDefinition\Parameter as BaseParameter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Parameter extends BaseParameter
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
        return 'text';
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
