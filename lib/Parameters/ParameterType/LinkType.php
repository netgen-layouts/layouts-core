<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
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
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry, ItemLoaderInterface $itemLoader)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
        $this->itemLoader = $itemLoader;
    }

    public function getIdentifier()
    {
        return 'link';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('value_types'));
        $optionsResolver->setAllowedTypes('value_types', 'array');
        $optionsResolver->setDefault('value_types', array());

        $optionsResolver->setNormalizer(
            'value_types',
            function (Options $options, $value) {
                if (!empty($value)) {
                    return $value;
                }

                return array_keys(
                    $this->valueTypeRegistry->getValueTypes(true)
                );
            }
        );
    }

    public function toHash(ParameterInterface $parameter, $value)
    {
        if (!$value instanceof LinkValue) {
            return null;
        }

        return array(
            'link_type' => $value->getLinkType(),
            'link' => $value->getLink(),
            'link_suffix' => $value->getLinkSuffix(),
            'new_window' => $value->getNewWindow(),
        );
    }

    public function export(ParameterInterface $parameter, $value)
    {
        if (!$value instanceof LinkValue) {
            return null;
        }

        $valueLink = $value->getLink();

        // If the link is internal, we need to convert the format
        // from value_type://value_id to value_type://remote_id
        if ($value->getLinkType() === LinkValue::LINK_TYPE_INTERNAL) {
            $valueLink = 'null://0';

            $link = parse_url($value->getLink());
            if (is_array($link) && isset($link['host']) && isset($link['scheme'])) {
                try {
                    $item = $this->itemLoader->load($link['host'], str_replace('-', '_', $link['scheme']));
                    $valueLink = str_replace('_', '-', $item->getValueType()) . '://' . $item->getRemoteId();
                } catch (ItemException $e) {
                    // Do nothing
                }
            }
        }

        return array(
            'link_type' => $value->getLinkType(),
            'link' => $valueLink,
            'link_suffix' => $value->getLinkSuffix(),
            'new_window' => $value->getNewWindow(),
        );
    }

    public function fromHash(ParameterInterface $parameter, $value)
    {
        if (!is_array($value) || empty($value['link_type'])) {
            return new LinkValue();
        }

        return new LinkValue(
            array(
                'linkType' => $value['link_type'],
                'link' => isset($value['link']) ? $value['link'] : null,
                'linkSuffix' => isset($value['link_suffix']) ? $value['link_suffix'] : null,
                'newWindow' => isset($value['new_window']) ? $value['new_window'] : false,
            )
        );
    }

    public function import(ParameterInterface $parameter, $value)
    {
        if (!is_array($value) || empty($value['link_type'])) {
            return new LinkValue();
        }

        $valueLink = isset($value['link']) ? $value['link'] : null;

        // If the link is internal, we need to convert the format
        // from value_type://remote_id to value_type://value_id
        if ($value['link_type'] === LinkValue::LINK_TYPE_INTERNAL) {
            $link = parse_url($valueLink !== null ? $valueLink : 'null://0');

            $valueLink = 'null://0';
            if (is_array($link) && isset($link['host']) && isset($link['scheme'])) {
                try {
                    $item = $this->itemLoader->loadByRemoteId($link['host'], str_replace('-', '_', $link['scheme']));
                    $valueLink = str_replace('_', '-', $item->getValueType()) . '://' . $item->getValueId();
                } catch (ItemException $e) {
                    // Do nothing
                }
            }
        }

        return new LinkValue(
            array(
                'linkType' => $value['link_type'],
                'link' => $valueLink,
                'linkSuffix' => isset($value['link_suffix']) ? $value['link_suffix'] : null,
                'newWindow' => isset($value['new_window']) ? $value['new_window'] : false,
            )
        );
    }

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        if (!$value instanceof LinkValue) {
            return true;
        }

        return empty($value->getLinkType()) || empty($value->getLink());
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(array('type' => LinkValue::class)),
            new LinkConstraint(
                array(
                    'required' => $parameter->isRequired(),
                    'valueTypes' => $parameter->getOption('value_types'),
                )
            ),
        );
    }
}
