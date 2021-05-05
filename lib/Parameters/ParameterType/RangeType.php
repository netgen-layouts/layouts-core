<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an integer specified
 * between provided minimum and maximum value.
 */
final class RangeType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'range';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(['min', 'max']);

        $optionsResolver->setAllowedTypes('min', 'int');
        $optionsResolver->setAllowedTypes('max', 'int');

        $optionsResolver->setNormalizer(
            'max',
            static fn (Options $options, int $value): int => $value < $options['min'] ?
                    $options['min'] :
                    $value,
        );

        $optionsResolver->setDefault(
            'default_value',
            static fn (Options $options, $previousValue) => $options['required'] === true ?
                    $options['min'] :
                    $previousValue,
        );
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\Range(
                [
                    'min' => $parameterDefinition->getOption('min'),
                    'max' => $parameterDefinition->getOption('max'),
                ],
            ),
        ];
    }
}
