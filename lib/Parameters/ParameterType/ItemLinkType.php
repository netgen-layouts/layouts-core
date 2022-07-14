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
    private ValueTypeRegistry $valueTypeRegistry;

    private RemoteIdConverter $remoteIdConverter;

    public function __construct(ValueTypeRegistry $valueTypeRegistry, RemoteIdConverter $remoteIdConverter)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
        $this->remoteIdConverter = $remoteIdConverter;
    }

    public static function getIdentifier(): string
    {
        return 'item_link';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(['value_types', 'allow_invalid']);
        $optionsResolver->setAllowedTypes('allow_invalid', 'bool');
        $optionsResolver->setAllowedTypes('value_types', 'string[]');

        $optionsResolver->setDefault('value_types', []);
        $optionsResolver->setDefault('allow_invalid', false);

        $optionsResolver->setNormalizer(
            'value_types',
            function (Options $options, array $value): array {
                if (count($value) > 0) {
                    return $value;
                }

                $valueTypes = [];

                /** @var \Netgen\Layouts\Item\ValueType\ValueType $valueType */
                foreach ($this->valueTypeRegistry->getValueTypes(true) as $identifier => $valueType) {
                    if ($valueType->supportsManualItems()) {
                        $valueTypes[] = $identifier;
                    }
                }

                return $valueTypes;
            },
        );
    }

    public function export(ParameterDefinition $parameterDefinition, $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        return $this->remoteIdConverter->convertToRemoteId($value);
    }

    public function import(ParameterDefinition $parameterDefinition, $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        return $this->remoteIdConverter->convertFromRemoteId($value);
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
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

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => 'string']),
            new ItemLinkConstraint(
                [
                    'valueTypes' => $parameterDefinition->getOption('value_types'),
                    'allowInvalid' => $parameterDefinition->getOption('allow_invalid'),
                ],
            ),
        ];
    }
}
