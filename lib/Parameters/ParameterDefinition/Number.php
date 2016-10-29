<?php

namespace Netgen\BlockManager\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Number extends ParameterDefinition
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'number';
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isRequired && $this->defaultValue === null) {
            return $this->options['min'];
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
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);
        $optionsResolver->setDefault('scale', 3);

        $optionsResolver->setRequired(array('min', 'max', 'scale'));

        $optionsResolver->setAllowedTypes('min', array('numeric', 'null'));
        $optionsResolver->setAllowedTypes('max', array('numeric', 'null'));
        $optionsResolver->setAllowedTypes('scale', array('int'));

        $optionsResolver->setNormalizer(
            'max',
            function (Options $options, $value) {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );
    }
}
