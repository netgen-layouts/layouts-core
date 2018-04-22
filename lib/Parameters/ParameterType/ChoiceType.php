<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a selection option.
 *
 * It can have a single value (string) or a multiple value (array of strings).
 */
final class ChoiceType extends ParameterType
{
    public function getIdentifier()
    {
        return 'choice';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('expanded', false);
        $optionsResolver->setRequired(['multiple', 'expanded', 'options']);
        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('expanded', 'bool');
        $optionsResolver->setAllowedTypes('options', ['array', 'callable']);

        $optionsResolver->setAllowedValues(
            'options',
            function ($value) {
                if (is_callable($value)) {
                    return true;
                }

                return !empty($value);
            }
        );

        $optionsResolver->setDefault(
            'default_value',
            function (Options $options, $previousValue) {
                if ($options['required']) {
                    if (!is_callable($options['options']) && !empty($options['options'])) {
                        $defaultValue = array_values($options['options'])[0];

                        return $options['multiple'] ? [$defaultValue] : $defaultValue;
                    }
                }

                return $previousValue;
            }
        );
    }

    public function fromHash(ParameterDefinition $parameterDefinition, $value)
    {
        if ($value === null || $value === []) {
            return;
        }

        if ($parameterDefinition->getOption('multiple')) {
            return is_array($value) ? $value : [$value];
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value)
    {
        return $value === null || $value === [];
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

        return [
            new Constraints\Choice(
                [
                    'choices' => array_values(
                        is_callable($options['options']) ?
                            $options['options']() :
                            $options['options']
                        ),
                    'multiple' => $options['multiple'],
                    'strict' => true,
                ]
            ),
        ];
    }
}
