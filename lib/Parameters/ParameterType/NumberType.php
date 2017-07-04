<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class NumberType extends ParameterType
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'number';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
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
     * Returns if the parameter value is empty.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null;
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

        $constraints = array(
            new Constraints\Type(
                array(
                    'type' => 'numeric',
                )
            ),
        );

        if ($options['min'] !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(
                array('value' => $options['min'])
            );
        }

        if ($options['max'] !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(
                array('value' => $options['max'])
            );
        }

        return $constraints;
    }
}
