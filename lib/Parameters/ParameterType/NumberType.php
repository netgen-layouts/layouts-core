<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate any kind of number.
 */
final class NumberType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'number';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->define('min')
            ->required()
            ->default(null)
            ->allowedTypes('numeric', 'null');

        $optionsResolver
            ->define('max')
            ->required()
            ->default(null)
            ->allowedTypes('numeric', 'null')
            ->normalize(
                static function (Options $options, $value) {
                    if ($value === null || $options['min'] === null) {
                        return $value;
                    }

                    if ($value < $options['min']) {
                        return $options['min'];
                    }

                    return $value;
                },
            );

        $optionsResolver
            ->define('scale')
            ->required()
            ->default(3)
            ->allowedTypes('int');

        $optionsResolver->setDefault(
            'default_value',
            static fn (Options $options, $previousValue) => $options['required'] === true ?
                    $options['min'] :
                    $previousValue,
        );
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        $min = $parameterDefinition->getOption('min');
        $max = $parameterDefinition->getOption('max');

        $constraints = [
            new Constraints\Type(type: 'numeric'),
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
