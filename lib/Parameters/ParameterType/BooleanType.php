<?php

declare(strict_types=1);

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
    public static function getIdentifier(): string
    {
        return 'boolean';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault(
            'default_value',
            function (Options $options, $previousValue) {
                if ($options['required'] === true) {
                    return false;
                }

                return $previousValue;
            }
        );
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null;
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
