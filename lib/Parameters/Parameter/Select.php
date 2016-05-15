<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class Select extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'select';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults(
            array(
                'multiple' => false,
            )
        );

        $optionsResolver->setRequired(array('multiple', 'options'));
        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('options', 'array');

        $optionsResolver->setAllowedValues(
            'options',
            function (array $value) {
                if (empty($value)) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getParameterConstraints()
    {
        return array(
            new Constraints\Choice(
                array(
                    'choices' => array_values($this->options['options']),
                    'multiple' => $this->options['multiple']
                )
            ),
        );
    }
}
