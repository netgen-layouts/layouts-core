<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an integer specified
 * between provided minimum and maximum value.
 */
final class RangeType extends ParameterType
{
    public function getIdentifier()
    {
        return 'range';
    }

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

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $value === null;
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

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
}
