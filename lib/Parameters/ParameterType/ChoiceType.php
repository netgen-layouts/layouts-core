<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
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
        $optionsResolver->setRequired(array('multiple', 'expanded', 'options'));
        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('expanded', 'bool');
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

        $optionsResolver->setDefault(
            'default_value',
            function (Options $options, $previousValue) {
                if ($options['required']) {
                    if (!is_callable($options['options']) && !empty($options['options'])) {
                        $defaultValue = array_values($options['options'])[0];

                        return $options['multiple'] ? array($defaultValue) : $defaultValue;
                    }
                }

                return $previousValue;
            }
        );
    }

    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if ($value === null || $value === array()) {
            return null;
        }

        if ($parameterDefinition->getOption('multiple')) {
            return is_array($value) ? $value : array($value);
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $value === null || $value === array();
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

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
}
