<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ChoiceType extends ParameterType
{
    /**
     * getIdentifierReturns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'choice';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
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

        $optionsResolver->setDefault('default_value', function (Options $options, $previousValue) {
            if ($options['required']) {
                if (!is_callable($options['options']) && !empty($options['options'])) {
                    return array_values($options['options'])[0];
                }
            }

            return $previousValue;
        });
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        $options = $parameter->getOptions();

        return array(
            new Constraints\Choice(
                array(
                    'choices' => array_values(
                        is_callable($options['options']) ?
                            $options['options']() :
                            $options['options']
                        ),
                    'multiple' => $options['multiple'],
                    'strict' => true,
                )
            ),
        );
    }

    /**
     * Returns if the parameter value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty($value)
    {
        return $value === null;
    }
}
