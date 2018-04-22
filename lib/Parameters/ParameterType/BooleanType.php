<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a boolean.
 */
final class BooleanType extends ParameterType
{
    public function getIdentifier()
    {
        return 'boolean';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault(
            'default_value',
            function (Options $options, $previousValue) {
                if ($options['required']) {
                    return false;
                }

                return $previousValue;
            }
        );
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value)
    {
        return $value === null;
    }

    protected function getRequiredConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        if ($parameterDefinition->isRequired()) {
            return [
                new Constraints\NotNull(),
            ];
        }

        return [];
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        return [
            new Constraints\Type(
                [
                    'type' => 'bool',
                ]
            ),
        ];
    }
}
