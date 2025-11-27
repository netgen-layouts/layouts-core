<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Validator\Constraint\Parameters\ItemLink as ItemLinkConstraint;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

use function count;
use function is_array;
use function is_string;
use function parse_url;

/**
 * Parameter type used to store and validate a link to an existing item in the system.
 */
final class ItemLinkType extends ParameterType
{
    public function __construct(
        private ValueTypeRegistry $valueTypeRegistry,
        private RemoteIdConverter $remoteIdConverter,
    ) {}

    public static function getIdentifier(): string
    {
        return 'item_link';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->define('value_types')
            ->required()
            ->default([])
            ->allowedTypes('string[]')
            ->normalize(
                function (Options $options, array $value): array {
                    if (count($value) > 0) {
                        return $value;
                    }

                    $valueTypes = [];

                    foreach ($this->valueTypeRegistry->getValueTypes(true) as $identifier => $valueType) {
                        if ($valueType->supportsManualItems) {
                            $valueTypes[] = $identifier;
                        }
                    }

                    return $valueTypes;
                },
            );

        $optionsResolver
            ->define('allow_invalid')
            ->required()
            ->default(false)
            ->allowedTypes('bool');
    }

    public function export(ParameterDefinition $parameterDefinition, mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        return $this->remoteIdConverter->convertToRemoteId($value);
    }

    public function import(ParameterDefinition $parameterDefinition, mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        return $this->remoteIdConverter->convertFromRemoteId($value);
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, mixed $value): bool
    {
        if (!is_string($value)) {
            return true;
        }

        $value = parse_url($value);
        if (!is_array($value)) {
            return true;
        }

        return ($value['scheme'] ?? '') === '' || !isset($value['host']);
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        return [
            new Constraints\Type(type: 'string'),
            new ItemLinkConstraint(
                valueTypes: $parameterDefinition->getOption('value_types'),
                allowInvalid: $parameterDefinition->getOption('allow_invalid'),
            ),
        ];
    }
}
