<?php

namespace Netgen\BlockManager\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    public function configureOptions(OptionsResolver $optionsResolver)
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
                if (empty($value) || isset($value[0])) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * Returns the Symfony form type which matches this parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * Maps the parameter attributes to Symfony form options.
     *
     * @return array
     */
    public function mapFormTypeOptions()
    {
        return array(
            'multiple' => $this->attributes['multiple'],
            'choices' => $this->attributes['options'],
        );
    }
}
