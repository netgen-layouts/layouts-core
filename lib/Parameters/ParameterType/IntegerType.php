<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
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
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);

        $optionsResolver->setRequired(['min', 'max']);

        $optionsResolver->setAllowedTypes('min', ['int', 'null']);
        $optionsResolver->setAllowedTypes('max', ['int', 'null']);

        $optionsResolver->setNormalizer(
            'max',
            function (Options $options, ?int $value): ?int {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );

        $optionsResolver->setDefault(
            'default_value',
            function (Options $options, $previousValue) {
                if ($options['required'] === true) {
                    return $options['min'];
                }

                return $previousValue;
            }
        );
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $min = $parameterDefinition->getOption('min');
        $max = $parameterDefinition->getOption('max');

        $constraints = [
            new Constraints\Type(['type' => 'int']),
        ];

        if ($min !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(['value' => $min]);
        }

        if ($max !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(['value' => $max]);
        }

        return $constraints;
    }
}
