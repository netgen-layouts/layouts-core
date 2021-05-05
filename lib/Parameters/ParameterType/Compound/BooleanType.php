<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType\Compound;

use Netgen\Layouts\Parameters\CompoundParameterType;
use Netgen\Layouts\Parameters\ParameterDefinition;
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
    public static function getIdentifier(): string
    {
        return 'compound_boolean';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(['reverse']);
        $optionsResolver->setAllowedTypes('reverse', 'bool');
        $optionsResolver->setDefault('reverse', false);

        $optionsResolver->setDefault(
            'default_value',
            static fn (Options $options, $previousValue) => $options['required'] === true ?
                    false :
                    $previousValue,
        );
    }

    protected function getRequiredConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        if ($parameterDefinition->isRequired()) {
            return [
                new Constraints\NotNull(),
            ];
        }

        return [];
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => 'bool']),
        ];
    }
}
