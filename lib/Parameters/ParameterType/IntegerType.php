<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an integer.
 */
final class IntegerType extends ParameterType
{
    public function getIdentifier()
    {
        return 'integer';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);

        $optionsResolver->setRequired(array('min', 'max'));

        $optionsResolver->setAllowedTypes('min', array('int', 'null'));
        $optionsResolver->setAllowedTypes('max', array('int', 'null'));

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

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null;
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        $options = $parameter->getOptions();

        $constraints = array(
            new Constraints\Type(
                array(
                    'type' => 'int',
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
