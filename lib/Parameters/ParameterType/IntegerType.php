<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an integer.
 */
final class IntegerType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'integer';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->define('min')
            ->required()
            ->default(null)
            ->allowedTypes('int', 'null');

        $optionsResolver
            ->define('max')
            ->required()
            ->default(null)
            ->allowedTypes('int', 'null')
            ->normalize(
                static fn (Options $options, ?int $value): ?int => match (true) {
                    $value === null || $options['min'] === null => $value,
                    $value < $options['min'] => $options['min'],
                    default => $value,
                },
            );

        $optionsResolver->setDefault(
            'default_value',
            static fn (Options $options, mixed $previousValue): mixed => match (true) {
                $options['required'] === true => $options['min'],
                default => $previousValue,
            },
        );
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        $min = $parameterDefinition->getOption('min');
        $max = $parameterDefinition->getOption('max');

        $constraints = [
            new Constraints\Type(type: 'int'),
        ];

        if ($min !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(value: $min);
        }

        if ($max !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(value: $max);
        }

        return $constraints;
    }
}
