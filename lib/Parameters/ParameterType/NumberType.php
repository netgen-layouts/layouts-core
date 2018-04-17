<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate any kind of number.
 */
final class NumberType extends ParameterType
{
    public function getIdentifier()
    {
        return 'number';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);
        $optionsResolver->setDefault('scale', 3);

        $optionsResolver->setRequired(['min', 'max', 'scale']);

        $optionsResolver->setAllowedTypes('min', ['numeric', 'null']);
        $optionsResolver->setAllowedTypes('max', ['numeric', 'null']);
        $optionsResolver->setAllowedTypes('scale', ['int']);

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

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $value === null;
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

        $constraints = [
            new Constraints\Type(
                [
                    'type' => 'numeric',
                ]
            ),
        ];

        if ($options['min'] !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(
                ['value' => $options['min']]
            );
        }

        if ($options['max'] !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(
                ['value' => $options['max']]
            );
        }

        return $constraints;
    }
}
