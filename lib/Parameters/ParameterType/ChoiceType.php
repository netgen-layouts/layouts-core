<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

use function array_values;
use function count;
use function is_array;
use function is_callable;

/**
 * Parameter type used to store and validate a selection option.
 *
 * It can have a single value (string) or a multiple value (array of strings).
 */
final class ChoiceType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'choice';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('expanded', false);
        $optionsResolver->setRequired(['multiple', 'expanded', 'options']);
        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('expanded', 'bool');
        $optionsResolver->setAllowedTypes('options', ['array', 'callable']);

        $optionsResolver->setAllowedValues(
            'options',
            static fn ($value): bool => is_callable($value) ? true : count($value) > 0,
        );

        $optionsResolver->setDefault(
            'default_value',
            static function (Options $options, $previousValue) {
                if ($options['required'] === true && !is_callable($options['options']) && count($options['options']) > 0) {
                    $defaultValue = array_values($options['options'])[0];

                    return $options['multiple'] === true ? [$defaultValue] : $defaultValue;
                }

                return $previousValue;
            },
        );
    }

    public function fromHash(ParameterDefinition $parameterDefinition, $value)
    {
        if ($value === null || $value === []) {
            return null;
        }

        if ($parameterDefinition->getOption('multiple') === true) {
            return is_array($value) ? $value : [$value];
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null || $value === [];
    }

    protected function getRequiredConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        if ($parameterDefinition->isRequired()) {
            return [
                new Constraints\NotNull(),
                new Constraints\NotIdenticalTo(['value' => []]),
            ];
        }

        return [];
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $options = $parameterDefinition->getOption('options');

        return [
            new Constraints\Choice(
                [
                    'choices' => array_values(is_callable($options) ? $options() : $options),
                    'multiple' => $parameterDefinition->getOption('multiple'),
                    'strict' => true,
                ],
            ),
        ];
    }
}
