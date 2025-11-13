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
        $optionsResolver
            ->define('multiple')
            ->required()
            ->default(false)
            ->allowedTypes('bool');

        $optionsResolver
            ->define('expanded')
            ->required()
            ->default(false)
            ->allowedTypes('bool');

        $optionsResolver
            ->define('options')
            ->required()
            ->allowedTypes('array', 'callable')
            ->allowedValues(static fn ($value): bool => is_callable($value) ? true : count($value) > 0)
            ->info('It must be a callable or a non-empty array.');

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

    public function fromHash(ParameterDefinition $parameterDefinition, mixed $value): mixed
    {
        if ($value === null || $value === []) {
            return null;
        }

        if ($parameterDefinition->getOption('multiple') === true) {
            return is_array($value) ? $value : [$value];
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, mixed $value): bool
    {
        return $value === null || $value === [];
    }

    protected function getRequiredConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        if ($parameterDefinition->isRequired()) {
            return [
                new Constraints\NotNull(),
                new Constraints\Count(min: 1),
            ];
        }

        return [];
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        $options = $parameterDefinition->getOption('options');

        return [
            new Constraints\Choice(
                choices: array_values(is_callable($options) ? $options() : $options),
                multiple: $parameterDefinition->getOption('multiple'),
                strict: true,
            ),
        ];
    }
}
