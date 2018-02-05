<?php

namespace Netgen\BlockManager\Parameters\ParameterType\Compound;

use Netgen\BlockManager\Parameters\CompoundParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a boolean.
 *
 * Difference of this parameter type from the regular boolean type is
 * that this type allows enabling/disabling validation of parameters
 * which are specified as "sub-parameters" of a parameter which has this type.
 *
 * If the boolean is checked, sub-parameters will be validated and stored,
 * and if the boolean is false, sub-parameter values will not be stored at all.
 */
final class BooleanType extends CompoundParameterType
{
    public function getIdentifier()
    {
        return 'compound_boolean';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('reverse'));
        $optionsResolver->setAllowedTypes('reverse', 'bool');
        $optionsResolver->setDefault('reverse', false);

        $optionsResolver->setDefault('default_value', function (Options $options, $previousValue) {
            if ($options['required']) {
                return false;
            }

            return $previousValue;
        });
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $value === null;
    }

    protected function getRequiredConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if ($parameterDefinition->isRequired()) {
            return array(
                new Constraints\NotNull(),
            );
        }

        return array();
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => 'bool',
                )
            ),
        );
    }
}
