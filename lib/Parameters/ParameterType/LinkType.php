<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Validator\Constraint\Parameters\Link as LinkConstraint;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a URL. Valid value for this type
 * is an object which is an instance of Netgen\BlockManager\Parameters\Value\LinkValue.
 */
final class LinkType extends ParameterType
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter
     */
    private $remoteIdConverter;

    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry, RemoteIdConverter $remoteIdConverter)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
        $this->remoteIdConverter = $remoteIdConverter;
    }

    public static function getIdentifier(): string
    {
        return 'link';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(['value_types', 'allow_invalid_internal']);
        $optionsResolver->setAllowedTypes('value_types', 'array');
        $optionsResolver->setAllowedTypes('allow_invalid_internal', 'bool');
        $optionsResolver->setAllowedTypes('value_types', 'string[]');

        $optionsResolver->setDefault('value_types', []);
        $optionsResolver->setDefault('allow_invalid_internal', false);

        $optionsResolver->setNormalizer(
            'value_types',
            function (Options $options, array $value): array {
                if (count($value) > 0) {
                    return $value;
                }

                return array_keys(
                    $this->valueTypeRegistry->getValueTypes(true)
                );
            }
        );
    }

    public function toHash(ParameterDefinition $parameterDefinition, $value)
    {
        if (!$value instanceof LinkValue) {
            return null;
        }

        return [
            'link_type' => $value->getLinkType(),
            'link' => $value->getLink(),
            'link_suffix' => $value->getLinkSuffix(),
            'new_window' => $value->getNewWindow(),
        ];
    }

    public function fromHash(ParameterDefinition $parameterDefinition, $value)
    {
        if (!is_array($value) || ($value['link_type'] ?? '') === '') {
            return new LinkValue();
        }

        return LinkValue::fromArray(
            [
                'linkType' => $value['link_type'],
                'link' => $value['link'] ?? null,
                'linkSuffix' => $value['link_suffix'] ?? null,
                'newWindow' => $value['new_window'] ?? false,
            ]
        );
    }

    public function export(ParameterDefinition $parameterDefinition, $value)
    {
        if (!$value instanceof LinkValue) {
            return null;
        }

        $valueLink = $value->getLink();

        // If the link is internal, we need to convert the format
        // from value_type://value to value_type://remote_id
        if ($value->getLinkType() === LinkValue::LINK_TYPE_INTERNAL) {
            $valueLink = $this->remoteIdConverter->convertToRemoteId($valueLink ?? '');
        }

        return [
            'link_type' => $value->getLinkType(),
            'link' => $valueLink,
            'link_suffix' => $value->getLinkSuffix(),
            'new_window' => $value->getNewWindow(),
        ];
    }

    public function import(ParameterDefinition $parameterDefinition, $value)
    {
        if (!is_array($value) || ($value['link_type'] ?? '') === '') {
            return new LinkValue();
        }

        $valueLink = $value['link'] ?? null;

        // If the link is internal, we need to convert the format
        // from value_type://remote_id to value_type://value
        if ($value['link_type'] === LinkValue::LINK_TYPE_INTERNAL) {
            $valueLink = $this->remoteIdConverter->convertFromRemoteId((string) $valueLink);
        }

        return LinkValue::fromArray(
            [
                'linkType' => $value['link_type'],
                'link' => $valueLink,
                'linkSuffix' => $value['link_suffix'] ?? null,
                'newWindow' => $value['new_window'] ?? false,
            ]
        );
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        if (!$value instanceof LinkValue) {
            return true;
        }

        if (($value->getLinkType() ?? '') === '') {
            return true;
        }

        if ($value->getLinkType() === LinkValue::LINK_TYPE_URL) {
            return ($value->getLink() ?? '') === '' && ($value->getLinkSuffix() ?? '') === '';
        }

        return ($value->getLink() ?? '') === '';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => LinkValue::class]),
            new LinkConstraint(
                [
                    'required' => $parameterDefinition->isRequired(),
                    'valueTypes' => $parameterDefinition->getOption('value_types'),
                    'allowInvalidInternal' => $parameterDefinition->getOption('allow_invalid_internal'),
                ]
            ),
        ];
    }
}
