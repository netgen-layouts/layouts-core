<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\Value\LinkType as LinkTypeEnum;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Validator\Constraint\Parameters\Link as LinkConstraint;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

use function count;
use function in_array;
use function is_array;

/**
 * Parameter type used to store and validate a URL. Valid value for this type
 * is an object which is an instance of Netgen\Layouts\Parameters\Value\LinkValue.
 */
final class LinkType extends ParameterType
{
    public function __construct(
        private ValueTypeRegistry $valueTypeRegistry,
        private RemoteIdConverter $remoteIdConverter,
    ) {}

    public static function getIdentifier(): string
    {
        return 'link';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->define('allow_invalid_internal')
            ->required()
            ->default(false)
            ->allowedTypes('bool');

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
    }

    /**
     * @return array<string, mixed>|null
     */
    public function toHash(ParameterDefinition $parameterDefinition, mixed $value): ?array
    {
        if (!$value instanceof LinkValue) {
            return null;
        }

        return [
            'link_type' => $value->linkType->value ?? '',
            'link' => $value->link,
            'link_suffix' => $value->linkSuffix,
            'new_window' => $value->newWindow,
        ];
    }

    public function fromHash(ParameterDefinition $parameterDefinition, mixed $value): LinkValue
    {
        if (!is_array($value) || ($value['link_type'] ?? '') === '') {
            return new LinkValue();
        }

        return LinkValue::fromArray(
            [
                'linkType' => LinkTypeEnum::tryFrom($value['link_type'] ?? ''),
                'link' => $value['link'] ?? '',
                'linkSuffix' => $value['link_suffix'] ?? '',
                'newWindow' => $value['new_window'] ?? false,
            ],
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function export(ParameterDefinition $parameterDefinition, mixed $value): ?array
    {
        if (!$value instanceof LinkValue) {
            return null;
        }

        $valueLink = $value->link;

        // If the link is internal, we need to convert the format
        // from value_type://value to value_type://remote_id
        if ($value->linkType === LinkTypeEnum::Internal) {
            $valueLink = $this->remoteIdConverter->convertToRemoteId($valueLink);
        }

        return [
            'link_type' => $value->linkType->value ?? '',
            'link' => $valueLink,
            'link_suffix' => $value->linkSuffix,
            'new_window' => $value->newWindow,
        ];
    }

    public function import(ParameterDefinition $parameterDefinition, mixed $value): LinkValue
    {
        if (!is_array($value) || ($value['link_type'] ?? '') === '') {
            return new LinkValue();
        }

        $valueLink = $value['link'] ?? '';

        // If the link is internal, we need to convert the format
        // from value_type://remote_id to value_type://value
        if ($value['link_type'] === LinkTypeEnum::Internal->value) {
            $valueLink = $this->remoteIdConverter->convertFromRemoteId((string) $valueLink);
        }

        return LinkValue::fromArray(
            [
                'linkType' => LinkTypeEnum::tryFrom($value['link_type'] ?? ''),
                'link' => $valueLink,
                'linkSuffix' => $value['link_suffix'] ?? '',
                'newWindow' => $value['new_window'] ?? false,
            ],
        );
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, mixed $value): bool
    {
        if (!$value instanceof LinkValue) {
            return true;
        }

        if ($value->linkType === null) {
            return true;
        }

        if (in_array($value->linkType, [LinkTypeEnum::Url, LinkTypeEnum::RelativeUrl], true)) {
            return $value->link === '' && $value->linkSuffix === '';
        }

        return $value->link === '';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        return [
            new Constraints\Type(type: LinkValue::class),
            new LinkConstraint(
                isRequired: $parameterDefinition->isRequired,
                valueTypes: $parameterDefinition->getOption('value_types'),
                allowInvalidInternal: $parameterDefinition->getOption('allow_invalid_internal'),
            ),
        ];
    }
}
