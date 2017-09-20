<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

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
class LinkType extends ParameterType
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
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
