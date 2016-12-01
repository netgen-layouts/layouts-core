<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class RangeType extends ParameterType
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'range';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
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

        $optionsResolver->setDefault(
            'default_value',
            function (Options $options, $previousValue) {
                if ($options['required']) {
                    return $options['min'];
                }

                return $previousValue;
            }
        );
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
            new Constraints\Type(
                array(
                    'type' => 'numeric',
                )
            ),
            new Constraints\Range(
                array(
                    'min' => $options['min'],
                    'max' => $options['max'],
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
