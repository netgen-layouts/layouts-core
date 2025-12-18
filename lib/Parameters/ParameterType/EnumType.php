<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use BackedEnum;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use ValueError;

use function array_first;
use function array_map;
use function is_a;
use function is_array;

/**
 * Parameter type used to store and validate an enum value.
 *
 * It can have a single value or a multiple value.
 */
final class EnumType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'enum';
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
            ->define('class')
            ->required()
            ->allowedTypes('string')
            ->allowedValues(static fn (string $value): bool => is_a($value, BackedEnum::class, true))
            ->info('It must be a valid backed enum.');

        $optionsResolver
            ->define('option_label_prefix')
            ->required()
            ->default(null)
            ->allowedTypes('string', 'null');

        $optionsResolver->setDefault(
            'default_value',
            static function (Options $options, mixed $previousValue): mixed {
                if ($options['required'] === true) {
                    $defaultValue = array_first($options['class']::cases());

                    return $options['multiple'] === true ? [$defaultValue] : $defaultValue;
                }

                return $previousValue;
            },
        );
    }

    /**
     * @return \BackedEnum|\BackedEnum[]|null
     */
    public function fromHash(ParameterDefinition $parameterDefinition, mixed $value): BackedEnum|array|null
    {
        if ($value === null || $value === []) {
            return null;
        }

        $enumClass = $parameterDefinition->getOption('class');

        if ($parameterDefinition->getOption('multiple') === true) {
            $values = [];

            foreach ((array) $value as $enumValue) {
                try {
                    $values[] = $enumClass::from($enumValue);
                } catch (ValueError) {
                    // Do nothing
                }
            }

            return $values;
        }

        return $enumClass::tryFrom(is_array($value) ? array_first($value) : $value);
    }

    /**
     * @return int|string|int[]|string[]|null
     */
    public function toHash(ParameterDefinition $parameterDefinition, mixed $value): int|string|array|null
    {
        if ($value === null || $value === []) {
            return null;
        }

        if ($parameterDefinition->getOption('multiple') === true) {
            return array_map(static fn (BackedEnum $enum): int|string => $enum->value, is_array($value) ? $value : [$value]);
        }

        return (is_array($value) ? array_first($value) : $value)->value;
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, mixed $value): bool
    {
        return $value === null || $value === [];
    }

    protected function getRequiredConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        if ($parameterDefinition->isRequired) {
            $constraints = [new Constraints\NotNull()];

            if (is_array($value)) {
                $constraints[] = new Constraints\Count(min: 1);
            }

            return $constraints;
        }

        return [];
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        return [
            new Constraints\Choice(
                choices: $parameterDefinition->getOption('class')::cases(),
                multiple: $parameterDefinition->getOption('multiple'),
            ),
        ];
    }
}
