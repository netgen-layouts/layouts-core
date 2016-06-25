<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class Range extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'range';
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
        $optionsResolver->setRequired(array('min', 'max'));

        $optionsResolver->setAllowedTypes('min', 'int');
        $optionsResolver->setAllowedTypes('max', 'int');

        $optionsResolver->setNormalizer(
            'max',
            function (Options $options, $value) {
                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints()
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => 'numeric',
                )
            ),
            new Constraints\Range(
                array(
                    'min' => $this->options['min'],
                    'max' => $this->options['max'],
                )
            ),
        );
    }
}
