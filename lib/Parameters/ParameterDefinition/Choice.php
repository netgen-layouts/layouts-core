<?php

namespace Netgen\BlockManager\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Choice extends ParameterDefinition
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'choice';
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isRequired && $this->defaultValue === null) {
            if (!is_callable($this->options['options']) && !empty($this->options['options'])) {
                return array_values($this->options['options'])[0];
            }
        }

        return parent::getDefaultValue();
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setRequired(array('multiple', 'options'));
        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('options', array('array', 'callable'));

        $optionsResolver->setAllowedValues(
            'options',
            function ($value) {
                if (is_callable($value)) {
                    return true;
                }

                return !empty($value);
            }
        );
    }
}
